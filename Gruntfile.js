module.exports = function (grunt) {

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        cssmin: {
            target: {
                files: [
                    {
                        expand: true,
                        cwd: 'assets',
                        src: ['*.css', '!*.min.css'],
                        dest: 'assets',
                        ext: '.min.css'
                    },
                    {
                        expand: true,
                        cwd: 'framework/assets',
                        src: ['*.css', '!*.min.css'],
                        dest: 'framework/assets',
                        ext: '.min.css'
                    }
                ]
            }
        },
        makepot: {
            target: {
                options: {
                    cwd: '',                          // Directory of files to internationalize.
                    domainPath: 'languages',                   // Where to save the POT file.
                    exclude: [],                      // List of files or directories to ignore.
                    include: [
                        'app/.*',
                        'framework/.*'
                    ],                      // List of files or directories to include.
                    mainFile: 'jobs-experts.php',                     // Main project file.
                    potComments: '',                  // The copyright at the beginning of the POT file.
                    potFilename: 'jbp.po',                  // Name of the POT file.
                    potHeaders: {
                        poedit: true,                 // Includes common Poedit headers.
                        'x-poedit-keywordslist': true // Include a list of all possible gettext functions.
                    },                                // Headers to add to the generated POT file.
                    processPot: null,                 // A callback function for manipulating the POT file.
                    type: 'wp-plugin',                // Type of project (wp-plugin or wp-theme).
                    updateTimestamp: true,             // Whether the POT-Creation-Date should be updated without other changes.
                    updatePoFiles: false              // Whether to update PO files in the same directory as the POT file.
                }
            }
        }
    });

    // Load the plugin that provides the "uglify" task.
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-wp-i18n');

    // Default task(s).
    grunt.registerTask('default', ['cssmin']);

};