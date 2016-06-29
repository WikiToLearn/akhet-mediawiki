#!/bin/bash
cd $(dirname "$(readlink -f $0)")

if false ; then
  rm -Rfv /home/thenerd/WTL/WikiToLearnHome/WikiToLearn/extensions/Akhet/
  if grep Akhet /home/thenerd/WTL/WikiToLearnHome/WikiToLearn/LocalSettings.php ; then
    sed -i '/Akhet/d' /home/thenerd/WTL/WikiToLearnHome/WikiToLearn/LocalSettings.php
  fi
  exit
else
  if ! grep Akhet /home/thenerd/WTL/WikiToLearnHome/WikiToLearn/LocalSettings.php ; then
    {
      echo "wfLoadExtension( 'Akhet' );"
      echo 'if (file_exists("$IP/../LocalSettings.d/Akhet.php")) { require_once("$IP/../LocalSettings.d/Akhet.php"); }'
    }  >> /home/thenerd/WTL/WikiToLearnHome/WikiToLearn/LocalSettings.php
  fi

  rsync \
  --delete-after \
  --exclude .git \
  --exclude deploy.sh \
  --delete-excluded \
  -av \
  . /home/thenerd/WTL/WikiToLearnHome/WikiToLearn/extensions/Akhet/
fi
