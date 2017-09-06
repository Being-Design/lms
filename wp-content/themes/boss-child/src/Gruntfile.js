module.exports = function (grunt) {

  // configure output directories
  var 
  JSDIR = {
    uncompressed: '../resources/js/uncompressed',
    libs: '../resources/js/uncompressed/libs', 
    compressed: '../resources/js/compressed'
  },
  CSSDIR = {
    uncompressed: '../resources/css/uncompressed',
    libs: '../resources/css/uncompressed', 
    compressed: '../resources/css/compressed'
  },
  IMGDIR = '../resources/images',
  FONTDIR = '../resources/fonts',
  RESOURCEDIR = '../resources';

  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    //sass compilation
    compass: {
      build: {
        options: {
          debugInfo: false,
          sourcemap: true,
          importPath: 'node_modules/',
          sassDir: 'scss/',
          cssDir: CSSDIR.uncompressed
        }
      }
    },
    less: {
      paths: ['scss/'],
      files: {

      },
      sourceMap: true
    },
    // css minification
    cssmin: {
      options: {
        keepSpecialComments: 0
      },
      dist: {
        files: [{
          expand: true,
          cwd: CSSDIR.libs,
          src: ['*.css'],
          dest: CSSDIR.compressed,
          ext: '.css'
        }
        ]
      },
      dev: {
        files: [{
          expand: true,
          cwd: CSSDIR.uncompressed,
          src: ['*.css'],
          dest: CSSDIR.compressed,
          ext: '.css'
        }
        ]
      }
    },
    // css & js concatenation
    concat: {
      dist: {
        files: {
          // css libraries
          [CSSDIR.libs + '/libraries.css']: [
          // 'bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.css',
          //'bower_components/jquery-selectric/public/selectric.css',
          //'bower_components/lity/dist/lity.css',
          ],
          // js libraries + custom
          [JSDIR.libs + '/00.jQuery.js']: [
          // 'bower_components/jquery/dist/jquery.js'
          ],
          [JSDIR.libs + '/01.bootstrap.js']: [
            // 'bootstrap/javascripts/bootstrap/collapse.js',
            // 'bootstrap/javascripts/bootstrap/dropdown.js',
            // 'bootstrap/javascripts/bootstrap/modal.js',
            // 'bootstrap/javascripts/bootstrap/transition.js',
            // 'bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.js',
            ],
            [JSDIR.libs + '/02.gsap.js']: [
            // 'bower_components/gsap/src/uncompressed/TweenMax.js',
            // 'bower_components/gsap/src/uncompressed/plugins/ScrollToPlugin.js'
            ],
            [JSDIR.libs + '/03.readmore.js']: [
            'bower_components/readmore-js/readmore.js'
            ],
          [JSDIR.uncompressed + '/libraries.js']: [JSDIR.libs + '/{,*/}*.js'],
        [JSDIR.uncompressed + '/main.js']: ['js/{,*/}*.js']
      }
    },
    'dev-js': {
      options: {
        separator: '\n',
      },
      files: {
            // js
          [JSDIR.uncompressed + '/main.js']: ['js/{,*/}*.js']
        }
      }
    },
    // js minification
    uglify: {
      dist: {
        files: {
          [JSDIR.compressed + '/libraries.js']: [JSDIR.uncompressed + '/libraries.js'],
          [JSDIR.compressed + '/main.js']: [JSDIR.uncompressed + '/main.js']
        }
      },
      dev: {
        files: {
          [JSDIR.compressed + '/main.js']: [JSDIR.uncompressed + '/main.js']
        }
      }
    },
    modernizr: {
      dist: {
        "crawl": false,
        "customTests": [],
        "devFile": JSDIR.uncompressed + "/modernizr.js",
        "dest": JSDIR.compressed + "/modernizr.js",
        "tests": [
        "flexboxtweener",
        "flexwrap"
        ],
        "options": [
        "setClasses"
        ],
        "uglify": true
      }
    },
    // in future, compress these!
    copy: {
      images: {
        expand: true,
        cwd: 'images',
        src: '**',
        dest: IMGDIR
      },
      fonts: {
        expand: true,
        cwd: 'bootstrap/fonts/bootstrap',
        src: '**',
        dest: FONTDIR
      }
    },
    // automate build on save
    watch: {
      js: {
      files: ['js/{,*/}*.js'],
      tasks: ['buildjs']
    },
    css: {
      files: 'scss/**/*.scss',
      tasks: ['buildcss']
    },
    img: {
    files: 'images/{,*/}*',
    tasks: ['buildimg']
  }
}
});

  // load all from node_modules
  require('load-grunt-tasks')(grunt);

  // define tasks
  grunt.registerTask('buildcss', ['compass', 'cssmin:dev']);
  grunt.registerTask('buildjs', ['concat:dev-js', 'uglify:dev']);
  grunt.registerTask('buildimg', ['copy:images']);
  grunt.registerTask('buildfonts', ['copy:fonts']);
  grunt.registerTask('default', ['buildcss', 'buildjs']);
  grunt.registerTask('initialize', ['buildimg','buildfonts','compass', 'concat:dist', 'cssmin:dist', 'cssmin:dev', 'uglify:dist','modernizr']);
  grunt.registerTask('buildall',['initialize']); // alias
};
