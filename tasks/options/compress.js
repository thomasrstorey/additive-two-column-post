module.exports = {
	main: {
		options: {
			mode: 'zip',
			archive: './release/additive_tcp.<%= pkg.version %>.zip'
		},
		expand: true,
		cwd: 'release/<%= pkg.version %>/',
		src: ['**/*'],
		dest: 'additive_tcp/'
	}
};