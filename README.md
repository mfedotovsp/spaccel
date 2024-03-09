# yii2-app-basic-docker-nginx-fpm-mysql

- [Initialization](#initialization)
- [Initializing an existing project](#initializing-an-existing-project)
- [Starting an existing project](#starting-an-existing-project)

## Initialization

> You can set environment variables in the [`.env`](.env) file

``` sh
git clone https://github.com/w3lifer/yii2-app-basic-docker-nginx-fpm-mysql yii2-app-basic
cd yii2-app-basic
make __initialization
```

After initialization, the `.git` directory will be deleted and the new repository will be created

Hence, you can add your remote `origin` to the newly created repository, commit and push the initial commit:

``` sh
git remote add origin git@github.com:<user>/<repo>
git add .
git commit -m 'initial commit'
git push -u origin master
```

> To access the app, open http://localhost:800 in your favorite browser (see [`.env`](.env) file)

## Initializing an existing project

``` sh
git clone git@github.com:<user>/<repo>
cd <repo>
make __initialization-an-existing-project
```

## Starting an existing project

``` sh
cd /path/to/project && make start
```
----------------------------------------
https://github.com/w3lifer/yii2-app-basic-docker-nginx-fpm-mysql/tree/master
----------------------------------------
