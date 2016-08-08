module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    watch: {
      clear: {
        //clear terminal on any watch task. beauty.
        files: ['**/**/*.php'],
        tasks: ['clear']
      },
      scripts: {
        files: ['**/**/*.php'],
        tasks: ['phpunit'],
        options: {
          spawn: true,
        },
      },
    },
    phpunit: {
      classes: {
        dir: 'tests/'
      },
      options: {
        bin: 'vendor/bin/phpunit',
        colors: true,
        followOutput: true,
        excludeGroup: 'internet',
        coverageHtml: 'coverage/'
      }
    }
  });

  grunt.loadNpmTasks('grunt-clear');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-phpunit');

  grunt.registerTask('default', ['watch']);
};
