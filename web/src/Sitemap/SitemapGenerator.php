<?php

namespace App\Sitemap;

use ReflectionClass;
use ReflectionMethod;
use App\Sitemap\SitemapRoute;
use Doctrine\Persistence\ManagerRegistry;
use HaydenPierce\ClassFinder\ClassFinder;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SitemapGenerator
{
    private string $schemeAndHost;

    public function __construct(
        private RequestStack $requestStack,
        private ManagerRegistry $em,
        private ParameterBagInterface $parameters
    ) {
        $this->schemeAndHost = $requestStack->getCurrentRequest()->getSchemeAndHttpHost();
    }

    /**
     * Generate the sitemap data
     *
     * @return array sitemap data
     */
    public function generateSitemapData(): array
    {
        $controllerClasses = $this->foundControllersClasses();
        $result = $this->iterateClassesAndProcessMethods($controllerClasses);

        usort($result, function ($a, $b) {
            return $a['priority'] < $b['priority'];
        });

        return $result;
    }

    /**
     * Retourne la liste des controllers éligible
     *
     * @return array
     */
    private function foundControllersClasses(): array
    {
        return array_filter(ClassFinder::getClassesInNamespace("App\Controller"), function (string $value) {
            return str_contains($value, "App\Controller\\") && !str_contains($value, "App\Controller\Admin\\");
        });
    }

    /**
     * Créer et  retourne l'ensemble des données lié au sitemap
     *
     * @param array $controllerClasses
     * @return array
     */
    private function iterateClassesAndProcessMethods(array $controllerClasses): array
    {
        $allResults =  [];

        foreach ($controllerClasses as $class) {
            $reflection = new ReflectionClass($class);
            $attributesClass = $reflection->getAttributes(Route::class);
            
            $methods = $this->filterMethods($reflection, $class);

            /** @var ReflectionMethod */
            foreach ($methods as $method) {
                $sitemapAttrArgs = $method->getAttributes(SitemapRoute::class)[0]->getArguments();
                $routeAttrArgs = $method->getAttributes(Route::class)[0]->getArguments();
                $url = $routeAttrArgs[0];

                if (isset($sitemapAttrArgs['repositoryClassQuery'])
                    && isset($sitemapAttrArgs['entityClass'])
                    && isset($sitemapAttrArgs['methodQuery'])
                ) {
                    /** @var ServiceEntityRepository */
                    $repository = new $sitemapAttrArgs['repositoryClassQuery']($this->em);
                    $query = $sitemapAttrArgs['methodQuery'];

                    $allEntity = $repository->$query();

                    $reflectionEntity = new ReflectionClass($sitemapAttrArgs['entityClass']);

                    if (preg_match_all("(\{[\w]+\})", $url, $params)) {
                        $urlParams = $this->extractUrlParamsFormUrl(
                            reflectionEntity: $reflectionEntity,
                            params: $params
                        );

                        foreach ($allEntity as $entity) {
                            $newUrl = $this->replaceUrlParamsByValueInUrl(
                                newUrl: $url,
                                urlParams: $urlParams,
                                entity: $entity
                            );

                            if (isset(
                                $sitemapAttrArgs['paginationParam']
                            )
                                && isset($sitemapAttrArgs['methodNb'])
                                && in_array("page", $urlParams)
                            ) {
                                $nbPage = $this->getNbPages(
                                    repositoryClassNb: $sitemapAttrArgs['repositoryClassNb'],
                                    methodNb: $sitemapAttrArgs['methodNb'],
                                    paginationParam: $sitemapAttrArgs['paginationParam'],
                                    nbCanHaveArgument: true,
                                    entity: $entity
                                );

                                $allPageUrl = $this->generateAllPageUrlEntry(
                                    method: $method,
                                    attributesClass: $attributesClass,
                                    nbPage: $nbPage,
                                    urlRaw: $newUrl
                                );

                                $allResults = [...$allResults, ...$allPageUrl];

                                continue;
                            }

                            $allResults[] = $this->formatSitemapEntry($method, $attributesClass, $newUrl);
                        }

                        continue;
                    }
                        
                    throw new Exception("Il n'y a pas de paramètre dans l'url");
                } elseif (isset($sitemapAttrArgs['repositoryClassNb'])
                    && isset($sitemapAttrArgs['methodNb'])
                    && isset($sitemapAttrArgs['paginationParam'])
                ) {
                    $urlNew = $routeAttrArgs[0];

                    $nbPage = $this->getNbPages(
                        repositoryClassNb: $sitemapAttrArgs['repositoryClassNb'],
                        methodNb: $sitemapAttrArgs['methodNb'],
                        paginationParam: $sitemapAttrArgs['paginationParam']
                    );
                    
                    $allPageUrl = $this->generateAllPageUrlEntry(
                        method: $method,
                        attributesClass: $attributesClass,
                        nbPage: $nbPage,
                        urlRaw: $urlNew
                    );

                    $allResults = [...$allResults,...$allPageUrl];
                } else {
                    $allResults[] = $this->formatSitemapEntry($method, $attributesClass);
                }
            }
        }

        return $allResults;
    }

    /**
     * Remplace les paramètre d'une url avec les valueurs de l'entité correspondante
     *
     * @param string $newUrl
     * @param array $urlParams
     * @param object $entity
     * @return string
     */
    private function replaceUrlParamsByValueInUrl(string $newUrl, array $urlParams, object $entity): string
    {
        foreach ($urlParams as $urlParam) {
            if ($urlParam == "page") {
                continue;
            }

            $paramValue = $entity->{"get".ucfirst($urlParam)}();
            $newUrl = str_replace("{".$urlParam."}", $paramValue, $newUrl);
        }

        return $newUrl;
    }

    /**
     * Extrait les paramètre d'une url
     *
     * @param ReflectionClass $reflectionEntity
     * @param array $params
     * @return array
     */
    private function extractUrlParamsFormUrl(ReflectionClass $reflectionEntity, array $params): array
    {
        $urlParams = [];

        foreach ($params as $value) {
            foreach ($value as $param) {
                if ($param == "{page}") {
                    $urlParams[] = "page";
                    continue;
                }

                $param = str_replace("{", "", $param);
                $param = str_replace("}", "", $param);

                $urlParams[] = $reflectionEntity->getProperty($param)->name;
            }
        }

        return $urlParams;
    }

    /**
     * Retourne le nombre de page totale
     *
     * @param string $repositoryClassNb
     * @param string $methodNb
     * @param string $paginationParam
     * @param boolean $nbCanHaveArgument
     * @param object|null $entity
     * @return integer
     */
    private function getNbPages(
        string $repositoryClassNb,
        string $methodNb,
        string $paginationParam,
        bool $nbCanHaveArgument = false,
        ?object $entity = null
    ): int {
        $repositoryNb = new $repositoryClassNb($this->em);
        $pagination = $this->parameters->get($paginationParam);

        if ($nbCanHaveArgument) {
            $reflectionMethod = new ReflectionMethod($repositoryNb, $methodNb);
            $nbMethodArgument = count($reflectionMethod->getParameters());

            if ($nbMethodArgument == 1) {
                $nb = $repositoryNb->$methodNb($entity);
            } elseif ($nbMethodArgument == 0) {
                $nb = $repositoryNb->$methodNb();
            } else {
                throw new Exception("La fonction choisi pour 'methodNb' demande trop d'arguments");
            }
        } else {
            $nb = $repositoryNb->$methodNb();
        }
        
        return ceil($nb/$pagination) == 0 ? 1 : ceil($nb/$pagination);
    }

    /**
     * Créer une liste d'url paginé
     *
     * @param ReflectionMethod $method
     * @param array $attributesClass
     * @param integer $nbPage
     * @param string $urlRaw
     * @return array
     */
    private function generateAllPageUrlEntry(
        ReflectionMethod $method,
        array $attributesClass,
        int $nbPage,
        string $urlRaw
    ): array {
        $result = [];

        for ($i=1; $i <= $nbPage; $i++) {
            if ($i == 1) {
                $urlLocal = str_replace("/{page}", "", $urlRaw);
                
                $result[] = $this->formatSitemapEntry($method, $attributesClass, $urlLocal);
                continue;
            }

            $urlLocal = str_replace("{page}", $i, $urlRaw);
            $result[] = $this->formatSitemapEntry($method, $attributesClass, $urlLocal);
        }

        return $result;
    }

    /**
     * Sélectionne les méthode ayant un attribut SitemapRoute
     *
     * @param ReflectionClass $reflection
     * @param string $class
     * @return array
     */
    private function filterMethods(ReflectionClass $reflection, string $class): array
    {
        return array_filter(
            $reflection->getMethods(ReflectionMethod::IS_PUBLIC),
            function (ReflectionMethod $value) use ($class) {
                return str_contains($value->class, $class) && !empty($value->getAttributes(SitemapRoute::class));
            }
        );
    }

    /**
     * Formate les informations lié a une url pour le sitemap
     *
     * @param ReflectionMethod $method
     * @param array $attributesClass
     * @param string|null $newUrl
     * @return array
     */
    private function formatSitemapEntry(ReflectionMethod $method, array $attributesClass, ?string $newUrl = null): array
    {
        $routeArgs = $method->getAttributes(Route::class)[0]->getArguments();
        $sitemapArgs = $method->getAttributes(SitemapRoute::class)[0]->getArguments();

        $schemeAndHost =  $this->schemeAndHost;
        $url = $routeArgs[0];

        if (!is_null($newUrl)) {
            $url = $newUrl;
        }

        if (!empty($attributesClass)) {
            $url = $attributesClass[0]->getArguments()[0].$url;
        }

        $url = $schemeAndHost.$url;

        return [
            "loc" => $url,
            "lastmod" => isset($sitemapArgs['lastMod']) ? $sitemapArgs['lastMod'] : null,
            "changefreq" => isset($sitemapArgs['changeFreq']) ? $sitemapArgs['changeFreq']: null,
            "priority" => isset($sitemapArgs['priority']) ? $sitemapArgs['priority']: null
        ];
    }
}
