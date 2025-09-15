#!/bin/bash
# Skrypt do kopiowania wszystkich plików do htdocs XAMPP

sudo chmod -R 755 ../web
rsync -av --delete ../web/ /opt/lampp/htdocs/
