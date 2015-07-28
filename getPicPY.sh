#!/bin/bash

#if cmp -s "$pyPicBot.py" "$ENV/bin/pyPicBot.py" then
#     echo 'files are the same'
#else
#    echo 'files are different'
#fi

cd ENV/bin
source activate
python -c "import pyPicBot; pyPicBot.getPic(\"$1\"); "