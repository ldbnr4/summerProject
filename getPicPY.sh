#!/bin/bash

cmp -s pyPicBot.py ENV/bin/pyPicBot.py > /dev/null
if [ $? -eq 1 ]; then
    cp pyPicBot.py ENV/bin
fi

cd ENV/bin
source activate
python -c "import pyPicBot; pyPicBot.getPic(\"$1\"); "