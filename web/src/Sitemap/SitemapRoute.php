<?php

namespace App\Sitemap;

use DateTime;
use Attribute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

#[Attribute(Attribute::TARGET_METHOD)]
class SitemapRoute
{
    public function __construct(
        private ?string $changeFreq = null,
        private ?float $priority = null,
        private ?DateTime $lastMod = null,
        private ?string $repositoryClassQuery = null,
        private ?string $repositoryClassNb = null,
        private ?string $entityClass = null,
        private ?string $paginationParam = null,
        private ?string $methodQuery = null,
        private ?string $methodNb = null
    ) {
    }

    /**
     * Get the value of changeFreq
     */
    public function getChangeFreq(): ?string
    {
        return $this->changeFreq;
    }

    /**
     * Set the value of changeFreq
     *
     * @return  self
     */
    public function setChangeFreq($changeFreq): self
    {
        $this->changeFreq = $changeFreq;
        return $this;
    }

    /**
     * Get the value of priority
     */
    public function getPriority(): ?float
    {
        return $this->priority;
    }

    /**
     * Set the value of priority
     *
     * @return  self
     */
    public function setPriority($priority): self
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * Get the value of lastMod
     */
    public function getLastMod(): ?DateTime
    {
        return $this->lastMod;
    }

    /**
     * Set the value of lastMod
     *
     * @return  self
     */
    public function setLastMod($lastMod): self
    {
        $this->lastMod = $lastMod;
        return $this;
    }

    /**
     * Get the value of repositoryClassQuery
     */
    public function getRepositoryClassQuery(): ?string
    {
        return $this->repositoryClassQuery;
    }

    /**
     * Set the value of repositoryClassQuery
     *
     * @return  self
     */
    public function setRepositoryClassQuery($repositoryClassQuery): self
    {
        $this->repositoryClassQuery = $repositoryClassQuery;

        return $this;
    }

    /**
     * Get the value of paginationParam
     */
    public function getPaginationParam(): ?string
    {
        return $this->paginationParam;
    }

    /**
     * Set the value of paginationParam
     *
     * @return  self
     */
    public function setPaginationParam($paginationParam): self
    {
        $this->paginationParam = $paginationParam;

        return $this;
    }

    /**
     * Get the value of methodQuery
     */
    public function getMethodQuery(): ?string
    {
        return $this->methodQuery;
    }

    /**
     * Set the value of methodQuery
     *
     * @return  self
     */
    public function setMethodQuery($methodQuery): self
    {
        $this->methodQuery = $methodQuery;

        return $this;
    }

    /**
     * Get the value of methodNb
     */
    public function getMethodNb(): ?string
    {
        return $this->methodNb;
    }

    /**
     * Set the value of methodNb
     *
     * @return  self
     */
    public function setMethodNb($methodNb): self
    {
        $this->methodNb = $methodNb;

        return $this;
    }

    public function isRequiredQuery(): bool
    {
        return !is_null($this->methodNb) || !is_null($this->methodQuery);
    }

    public function isCollection(): bool
    {
        return !is_null($this->methodNb);
    }

    public function isOneEntity(): bool
    {
        return !is_null($this->methodQuery);
    }

    /**
     * Get the value of entityClass
     */
    public function getEntityClass(): ?string
    {
        return $this->entityClass;
    }

    /**
     * Set the value of entityClass
     *
     * @return  self
     */
    public function setEntityClass($entityClass): self
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    /**
     * Get the value of repositoryClassNb
     */
    public function getRepositoryClassNb(): ?string
    {
        return $this->repositoryClassNb;
    }

    /**
     * Set the value of repositoryClassNb
     *
     * @return  self
     */
    public function setRepositoryClassNb($repositoryClassNb): self
    {
        $this->repositoryClassNb = $repositoryClassNb;

        return $this;
    }
}
