#!/bin/sh

set -e

cp git-hook/commit-msg .git/hooks
chmod ug+x .git/hooks/commit-msg
cp git-hook/pre-commit .git/hooks
chmod ug+x .git/hooks/pre-commit

echo "Git hooks ajoutés avec succès"