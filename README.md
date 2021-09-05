## About Laravel Lighthouse Template

Basically just my own personal Laravel + Lighthouse (GraphQL) seed to get quickly started with new projects :)

## Installation

### Prerequisites

I will be assuming you have following alias already
```
alias sail='./vendor/bin/sail'
```

### Initial setup

If you don't want to assume evnironment you can install the vendor packages (and Sail) with Docker
```
...
```

You want to migrate and maybe even seed the database
```
sail artisan migrate
sail artisan db:seed
```

### Starting the web server

This repository is pre-shipped with Sail, thus we can run
```
sail up -d
```
