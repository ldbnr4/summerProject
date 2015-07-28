#!/bin/bash
directory="./Scripts"

virtualenv ENV --system-site-packages
cd ENV
if [ -d $directory ]; then
  mv Scripts bin
fi
cd ..
cp pyDates.py ENV/bin
cp pyJamBaseBot.py ENV/bin
cp pyPicBot.py ENV/bin
cd ENV/bin
source activate
unamestr=`uname`
if [[ "$unamestr" == 'Linux' ]]; then
    pip install lxml -I
    pip install requests -I
    pip install urllib2 -I
    pip install HTMLParser -I
    pip install datetime -I
    pip install python-dateutil -I
fi