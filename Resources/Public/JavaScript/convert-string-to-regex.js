let apps = klaroConfig.apps;

listsOfCookies = apps.map(function (app) {
    return app.cookies
});

allCookies = listsOfCookies.forEach(function (list) {
    if (!list) return;

    list.forEach(function (cookie) {
        if (cookie && cookie[0] !== "") {
            cookie[0] = new RegExp(cookie[0])
        }
    });
});
