#!/bin/bash
echo '\nmid a: '
res=${PWD##*/}
echo $res

cd ENV/bin
source activate

echo '\nmid b: '
res=${PWD##*/}
echo $res

python -c "import pyJamBaseBot; pyJamBaseBot.getEvents(\"$1\"); "