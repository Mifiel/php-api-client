module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    watch: {
      scripts: {
        files: ['**/**/*.php'],
        tasks: ['phpunit'],
        options: {
          spawn: false,
        },
      },
    },
    phpunit: {
      classes: {
        dir: 'tests/'
      },
      options: {
        bin: 'vendor/bin/phpunit',
        colors: true
      }
    }
  });

  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-phpunit');

  grunt.registerTask('default', ['watch']);
};
