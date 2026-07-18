module.exports = function (grunt) {
	"use strict";

	// Change these for your project
	const SOURCE_PATH = "assets/src";
	const PUBLIC_PATH = "assets";

	// Leave as is
	const CSS_SRC = SOURCE_PATH + "/css";
	const CSS_DIST = PUBLIC_PATH + "/css";
	const CSS_VENDOR = SOURCE_PATH + "/css/vendor";
	const SCSS_SRC = SOURCE_PATH + "/scss";
	const SCSS_DIST = PUBLIC_PATH + "/css";

	grunt.initConfig({
		pkg: grunt.file.readJSON("package.json"),
		sass: {
			dist: {
				files: {
					[`${SCSS_DIST}/one.css`]: `${SCSS_SRC}/core.scss`,
					[`${SCSS_DIST}/vendor.css`]: `${SCSS_SRC}/vendor.scss`
				}
			}
		},
		concat_css: {
			dist: {
				files: {
					[`${CSS_DIST}/one.css`]: [
						`${CSS_SRC}/vars.css`,
						`${CSS_SRC}/utils.css`,
						`${CSS_SRC}/grid.css`,
						`${CSS_SRC}/base.css`,
						`${CSS_SRC}/main.css`,
						`${CSS_SRC}/print.css`
					],
					[`${CSS_DIST}/vendor.css`]: [`${CSS_VENDOR}/normalize.css`]
				}
			}
		},
		cssmin: {
			dist: {
				files: {
					[`${CSS_DIST}/one.css`]: [
						`${CSS_SRC}/vars.css`,
						`${CSS_SRC}/utils.css`,
						`${CSS_SRC}/grid.css`,
						`${CSS_SRC}/base.css`,
						`${CSS_SRC}/main.css`,
						`${CSS_SRC}/print.css`
					],
					[`${CSS_DIST}/vendor.css`]: [`${CSS_VENDOR}/normalize.css`]
				}
			},
			dist2: {
				files: {
					[`${CSS_DIST}/vendor.min.css`]: [`${CSS_DIST}/vendor.css`],
					[`${CSS_DIST}/one.min.css`]: [`${CSS_DIST}/one.css`]
				}
			}
		},
		postcss: {
			options: {
				map: {
					inline: false,
					annotation: `${CSS_DIST}/maps/`
				},
				processors: [
					// require('pixrem')(), // rem unit fallbacks (mostly for <=IE8 & IE9&IE10 just on font property)
					require("autoprefixer")(), // add vendor prefixes
					require("cssnano")({ preset: "default" })
				]
			},
			dist: {
				src: `${CSS_DIST}/one.css`
			}
		},
		watch: {
			css: {
				files: `${SOURCE_PATH}/**/*.css`,
				tasks: ["concat_css"],
				options: {
					livereload: true
				}
			},
			scss: {
				files: `${SOURCE_PATH}/**/*.scss`,
				tasks: ["sass"],
				options: {
					livereload: true
				}
			},
			configFiles: {
				files: ["Gruntfile.js"],
				options: {
					reload: true
				}
			}
		}
	});

	grunt.loadNpmTasks("grunt-contrib-cssmin");
	grunt.loadNpmTasks("grunt-contrib-sass");
	grunt.loadNpmTasks("grunt-contrib-watch");
	grunt.loadNpmTasks("grunt-concat-css");
	grunt.loadNpmTasks('@lodder/grunt-postcss');
	grunt.registerTask("default", ["watch"]);

	grunt.registerTask("prod", "Production Builder", function (styles = "css") {
		// Styles
		if (styles === "css") {
			grunt.task.run("concat_css");
		} else {
			grunt.task.run("sass");
		}
		grunt.task.run("postcss");
	});
};
