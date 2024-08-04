const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
    mode: 'development', // Use 'development' for debugging with source maps

    // Entry points of the application
    entry: './assets/js/index.js', // JavaScript entry point

    // Output configuration
    output: {
        filename: 'bundle.js', // Output JavaScript file name
        path: path.resolve(__dirname), // Output directory set to root
    },

    // Enable source maps
    devtool: 'inline-source-map', // Good for debugging, includes source maps in the bundled files

    // Plugins
    plugins: [
        new MiniCssExtractPlugin({
            filename: 'style.css', // Output CSS file name
        }),
    ],

    // Module rules for processing files
    module: {
        rules: [
            {
                test: /\.js$/, // Match all .js files
                exclude: /node_modules/, // Exclude the node_modules directory
                use: {
                    loader: 'babel-loader', // Use babel-loader for transpiling JS
                    options: {
                        presets: ['@babel/preset-env'], // Preset for transpiling ES6+
                    },
                },
            },
            {
                test: /\.scss$/, // Match all .scss files
                use: [
                    MiniCssExtractPlugin.loader, // Extract CSS into files
                    'css-loader', // Resolves CSS imports into JS
                    {
                        loader: 'sass-loader', // Loads and compiles SASS to CSS
                        options: {
                            sourceMap: true, // Enable source maps for Sass
                            sassOptions: {
                                outputStyle: 'compressed', // Minifies the CSS
                            },
                        },
                    },
                ],
            },
            {
                test: /\.css$/, // Match all .css files
                use: [
                    MiniCssExtractPlugin.loader, // Extract CSS into files
                    'css-loader', // Resolves CSS imports into JS
                ],
            },
        ],
    },
};
