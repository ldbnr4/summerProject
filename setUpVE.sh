#!/bin/bash
directory="./Scripts"

virtualenv ENV
cd ENV
if [ -d $directory ]; then
  mv Scripts bin
fi
cd ..
cp pyDates.py ENV/bin
cp pyJamBaseBot.py ENV/bin
cp pyPicBot.py ENV/bin
echo 'mid2 a: ' ${PWD##*/}
cd ENV/bin
echo 'mid2 b: ' ${PWD##*/}
source activate
unamestr=`uname`
if [[ "$unamestr" == 'Linux' ]]; then
    pip install lxml
    pip install requests
    pip install urllib2
    pip install HTMLParser
    pip install datetime
    pip install python-dateutil
fi