const path = require("path");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const postcssCustomProperties = require('postcss-custom-properties');
const postcssImport = require('postcss-import');
const postcssPresetEnv = require('postcss-preset-env');
const autoprefixer = require('autoprefixer');
const postcssNested = require('postcss-nested');
const globalCssPath = path.join(__dirname, "/src/styles/global.css");
module.exports = {
    entry: "./src/index.js",
    output: {
        path: path.join(__dirname, "/dist"),
        filename: "app.js"
    },
    module: {
        rules: [
            {
                test: /\.js|.jsx?$/,
                exclude: /node_modules/,
                use: [
                    {
                        loader: 'babel-loader',
                        options: {
                            presets: ["@babel/preset-env", "@babel/preset-react"]
                        }
                    }
                ]
            },
            {
                test: /\.css$/,
                exclude: /node_modules/,
                use: [MiniCssExtractPlugin.loader, {
                    loader: "css-loader",
                    options: {
                        url: false,
                        importLoaders: 1,
                        modules: {
                            localIdentName: "[name]__[local]___[hash:base64:7]"
                        }
                    }
                }, 
                { 
                    loader: 'postcss-loader',
                    options: {
                        ident: 'postcss',
                        plugins: () => [
                            postcssImport,
                            autoprefixer,
                            postcssNested,
                            postcssCustomProperties({
                                preserve: false,
                                importFrom: [ globalCssPath ]
                            })
                        ]
                    }
                }]
            }
        ]
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: "style.css"
        })
    ]
};