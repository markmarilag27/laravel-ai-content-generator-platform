import js from '@eslint/js';
import pluginVue from 'eslint-plugin-vue';
import vueTsEslintConfig from '@vue/eslint-config-typescript';
import prettierConfig from '@vue/eslint-config-prettier/skip-formatting';
import globals from 'globals';

export default [
  {
    ignores: ['node_modules/', 'public/', 'vendor/', 'resources/views/'],
  },
  js.configs.recommended,
  ...pluginVue.configs['flat/strongly-recommended'],
  ...vueTsEslintConfig(),
  prettierConfig,
  {
    languageOptions: {
      ecmaVersion: 'latest',
      sourceType: 'module',
      globals: {
        ...globals.browser,
        ...globals.node,
      },
    },
    rules: {
      'vue/block-order': [
        'error',
        {
          order: ['script', 'template', 'style'],
        },
      ],
      'vue/component-name-in-template-casing': ['error', 'PascalCase'],
      'vue/no-useless-v-bind': 'error',
      'vue/no-empty-component-block': 'error',
      'vue/no-duplicate-attr-inheritance': 'error',
      'vue/require-name-property': 'error',
      'vue/no-multiple-template-root': 'off',

      '@typescript-eslint/no-unused-vars': ['error', { argsIgnorePattern: '^_' }],
      '@typescript-eslint/explicit-module-boundary-types': 'error',
      '@typescript-eslint/ban-ts-comment': 'warn',

      'no-console': 'warn',
      'no-debugger': 'error',
    },
  },
];
