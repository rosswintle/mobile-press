#!/bin/sh
php mpress scrape
# Also in deploy script as we don't commit the results
npx pagefind
npx tailwindcss -i ./resources/css/styles.css -o ./public/css/styles.css
