module.exports = {
	dist: {
		options: {
			processors: [
				require('autoprefixer')({browsers: 'last 2 versions'})
			]
		},
		files: { 
			'assets/css/additive-two-column-post.css': [ 'assets/css/src/additive-two-column-post.css' ]
		}
	}
};