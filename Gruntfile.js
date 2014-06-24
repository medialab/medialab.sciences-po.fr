module.exports = function(grunt) {
  grunt.initConfig({
    less:{
      development: {
        options: {
          paths: ["assets/css"]
        },
        files: {
          "path/to/result.css": "path/to/source.less"
        }
      },
      production: {
        options: {
          paths: ["assets/css"],
          cleancss: true,
        },
        files: {
          "wp-content/themes/medialab_v4/style.css": "wp-content/themes/medialab_v4/style.less"
        }
      }
    }
  });

  console.log("\n                      *     .--.\n                           / /  `\n          +               | |\n                 '         \\ \\__,\n             *          +   '--'  *\n                 +   /\\\n    +              .'  '.   *\n           *      /======\\      +\n                 ;:.  _   ;\n                 |:. (_)  |\n                 |:.  _   |\n       +         |:. (_)  |          *\n                 ;:.      ;\n               .' \:.    /  `.\n              / .-'':._.'`-. \\\n              |/    /||\\    \\|\n        jgs _..--\"\"\"````\"\"\"--.._\n      _.-'``                    ``'-._\n    -'                                '-\n\n");
  console.log(grunt.cli.tasks.join(''));

  grunt.loadNpmTasks('grunt-contrib-less');

  grunt.registerTask('default', ['less:production']);
};
