var WebpackNotifierPlugin = require('webpack-notifier');

Elixir.webpack.mergeConfig({
    plugins: [
        new WebpackNotifierPlugin({alwaysNotify: true}),
    ]
});