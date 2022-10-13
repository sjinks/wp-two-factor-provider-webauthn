/* eslint-disable jsdoc/valid-types, no-console */
import { defineConfig } from 'rollup';
import typescript from '@rollup/plugin-typescript';
import { babel } from '@rollup/plugin-babel';
import { minify } from 'terser';
import codeFrameColumns from '@babel/code-frame';

/** @type {import('rollup').Plugin[]} */
const plugins = [
	typescript(),
	babel({
		babelHelpers: 'bundled',
		exclude: 'node_modules/**',
		extensions: ['.js', '.ts'],
		presets: [['@babel/env', { loose: true, bugfixes: true, modules: false }]],
		plugins: [['@wordpress/babel-plugin-makepot', { output: 'lang/two-factor-provider-webauthn-js.pot' }]],
		babelrc: false,
	}),
	{
		name: 'terser',
		async renderChunk(code, _chunk, outputOptions) {
			try {
				const result = await minify(code, {
					sourceMap: outputOptions.sourcemap === true || typeof outputOptions.sourcemap === 'string',
				});

				return {
					code: result.code,
					map: result.map,
				};
			} catch (error) {
				const { message, line, col: column } = error;
				console.error(codeFrameColumns(code, { start: { line, column } }, { message }));
				throw error;
			}
		},
	},
];

export default defineConfig([
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
		external: ['jquery', '@wordpress/i18n'],
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
		external: ['@wordpress/i18n'],
		plugins,
	},
]);
