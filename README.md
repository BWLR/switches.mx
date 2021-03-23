# Switches.mx
## The MX switches Database

Licensed under: [Attribution-NonCommercial-ShareAlike 4.0 International (CC BY-NC-SA 4.0)](https://creativecommons.org/licenses/by-nc-sa/4.0/)

If you have any questions, please feel free to contact me at any of the following:

Discord: BOWLER#2802 [Discord Server ](https://discord.gg/pZxjvza)

Reddit: [PM me /u/switchesmx](https://www.reddit.com/message/compose/?to=switchesmx)

## Project structure

The website is built using [Statamic](https://statamic.com/) - the Laravel PHP CMS. I run this locally using [Laravel Homestead](https://laravel.com/docs/8.x/homestead). The beauty of Statamic is that it's all self-contained with no external database; the content is all stored in yaml and markdown files in the `/content` directory.

To get it running inside a virtual machine you'll need to run `composer install` first.

To build local assets and then to run the watcher:
- `npm install`
- `npm run watch-poll`