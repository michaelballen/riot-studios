({
    appDir: "../",
    baseUrl: "js",
    dir: "../publish/riotstudios-b-1",
	paths: {
		"jquery": "//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min"
	},
	fileExclusionRegExp: /^\.|^(sass)|^(publish)|^(build)|^(config\.rb)|^(js\/node_modules)|^(js\/app\.build)|^(js\/plugins)|^(js\/vendor\/validation)|^(js\/vendor\/bootstrap)|^(js\/r\.js)/,
    modules: [
        {
            name: "about"
        },
		{
            name: "admin"
        },
		{
            name: "apply-page"
        },
		{
            name: "believe-me"
        },
		{
            name: "blog"
        },
		{
            name: "checkout"
		},
		{
            name: "form"
		},
		{
            name: "head"
		},
		{
            name: "home"
		},
		{
            name: "job-application"
		},
		{
            name: "request-an-event"
		},
		{
            name: "single"
		},
		{
            name: "store"
		},
		{
            name: "theater"
		}
    ]
})