module.exports = function (grunt) {
  grunt.registerTask( 'deploy', ['removeOld', 'deployLocal']);
}
