#!/bin/bash

TESTDIR=$(dirname $0);

CODECOVERAGE=1 \
COVERAGE='--coverage-html '$TESTDIR'/coverage' \
$TESTDIR/runtests.sh
