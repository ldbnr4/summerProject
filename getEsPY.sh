#!/bin/bash

cmp -s pyJamBaseBot.py ENV/bin/pyJamBaseBot.py > /dev/null
if [ $? -eq 1 ]; then
    cp pyJamBaseBot.py ENV/bin
fi

cd ENV/bin
source activate
python -c "import pyJamBaseBot; pyJamBaseBot.getEvents(\"$1\"); "