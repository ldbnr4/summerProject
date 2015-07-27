#!/bin/bash
# use predefined variables to access passed arguments
#echo arguments to the shell
if [ $2 == 0 ]
    then
        virtualenv ENV
        cp pyDates.py ENV/bin
        cp pyJamBaseBot.py ENV/bin
        cp pyPicBot.py ENV/bin
fi

cd ENV/bin
source activate

if [ $2 == 0 ]
    then
        pip install lxml
        pip install requests
        pip install urllib2
        pip install HTMLParser
        pip install datetime
        pip install python-dateutil
fi
python -c "import pyJamBaseBot; pyJamBaseBot.getEvents(\"$1\"); "