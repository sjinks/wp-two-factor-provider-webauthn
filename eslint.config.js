const { configs } = require( '@automattic/eslint-plugin-wpvip' );
const eslintPluginPlaywright = require( 'eslint-plugin-playwright' );

const config = [
	{
		ignores: [ 'vendor/**', 'assets/*.js' ],
	},
	...configs.recommended,
	...configs.typescript,
	eslintPluginPlaywright.configs[ 'flat/recommended' ],
	{
		rules: {
			'id-length': 'off',
		},
		linterOptions: {
			reportUnusedDisableDirectives: 'warn',
		},
	},
];

module.exports = config;
