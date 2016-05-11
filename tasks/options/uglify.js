module.exports = {
	all: {
		files: {
			'assets/js/additive-two-column-post.min.js': ['assets/js/additive-two-column-post.js']
		},
		options: {
			banner: '/*! <%= pkg.title %> - v<%= pkg.version %>\n' +
			' * <%= pkg.homepage %>\n' +
			' * Copyright (c) <%= grunt.template.today("yyyy") %>;' +
			' * Licensed MIT' +
			' */\n',
			mangle: {
				except: ['jQuery']
			}
		}
	}
};
