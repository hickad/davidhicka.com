const path = require('path');

module.exports = {
    // Mode: development or production (production will minify the code)
    mode: 'production',

    // Entry point of the application
    entry: './js/index.js', // Adjust this if your entry file has a different name

    // Output configuration
    output: {
        filename: 'bundle.js', // Output file name
        path: path.resolve(__dirname, 'dist'), // Output directory
    },

    // Module rules (you can add loaders here if needed)
    module: {
        rules: [
            {
                test: /\.js$/, // Regular expression to match all .js files
                exclude: /node_modules/, // Exclude the node_modules directory
                use: {
                    loader: 'babel-loader', // Loader to transpile ES6+ to ES5
                    options: {
                        presets: ['@babel/preset-env'] // Preset used for transpiling
                    }
                }
            }
        ]
    }
};
