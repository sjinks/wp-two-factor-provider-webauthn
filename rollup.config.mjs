import { babel } from '@rollup/plugin-babel';
import typescript from '@rollup/plugin-typescript';
import { terser } from '@wwa/rollup-plugin-terser';
import { defineConfig } from 'rollup';

/** @type {import('rollup').Plugin[]} */
const plugins = [
	typescript(),
	babel( {
		babelHelpers: 'bundled',
		exclude: 'node_modules/**',
		extensions: [ '.js', '.ts' ],
		presets: [ [ '@babel/env', { targets: 'baseline 2022', modules: false } ] ],
		plugins: [ [ '@wordpress/babel-plugin-makepot', { output: 'lang/two-factor-provider-webauthn-js.pot' } ] ],
		babelrc: false,
	} ),
	terser(),
];

export default defineConfig( [
	{
		input: 'assets/profile.ts',
		output: {
			file: 'assets/profile.min.js',
			sourcemap: 'hidden',
			format: 'iife',
			strict: false,
			globals: {
				jquery: 'jQuery',
				'@wordpress/i18n': 'wp.i18n',
			},
		},
		external: [ 'jquery', '@wordpress/i18n' ],
		plugins,
	},
	{
		input: 'assets/login.ts',
		output: {
			file: 'assets/login.min.js',
			sourcemap: 'hidden',
			format: 'iife',
			strict: false,
			globals: {
				'@wordpress/i18n': 'wp.i18n',
			},
		},
		external: [ '@wordpress/i18n' ],
		plugins,
	},
] );
