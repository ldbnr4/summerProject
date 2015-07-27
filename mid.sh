#!/bin/bash

cd ENV/bin
source activate

python -c "import pyJamBaseBot; pyJamBaseBot.getEvents(\"$1\"); "