#!/bin/bash

cd ENV/bin
source activate

python -c "import pyPicsiBot; pyPicBot.getPic(\"$1\"); "