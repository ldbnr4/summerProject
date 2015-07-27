#!/bin/bash
# use predefined variables to access passed arguments
#echo arguments to the shell

cd lilENV/bin
source activate

python -c "import pyJamBaseBot; pyJamBaseBot.getEvents(\"$1\"); "