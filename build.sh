#!/bin/sh
php mpress scrape
npx pagefind
npx tailwindcss -i ./resources/css/styles.css -o ./public/css/styles.css
