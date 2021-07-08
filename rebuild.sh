#!/bin/bash

#
# Use this script to build the dev environment
# and populate all the tables with enough data to 
# get working
#

vendor/bin/sail artisan migrate:fresh
vendor/bin/sail artisan db:seed --class=ProductionSeeder
vendor/bin/sail artisan db:seed --class=DevelopmentSeeder