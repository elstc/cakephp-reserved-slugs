# CakePHP Plugin プロジェクト構造

## ディレクトリ構造

```
my-plugin/
├── .editorconfig              # エディタ設定
├── .gitattributes             # Git属性（export-ignore等）
├── .gitignore                 # Git除外設定
├── .github/
│   └── workflows/
│       ├── ci.yml             # CI設定（必須）
│       └── stale.yml          # Stale issue管理（任意）
├── .phive/
│   └── phars.xml              # PHPStan等のPHARツール管理
├── composer.json              # Composer設定（必須）
├── LICENSE.txt                # ライセンスファイル（MIT推奨）
├── phpcs.xml                  # PHPCSルール設定
├── phpstan.neon               # PHPStan設定
├── phpstan-baseline.neon      # PHPStan ベースライン
├── phpunit.xml.dist           # PHPUnit設定
├── README.md                  # プロジェクト説明
├── src/                       # ソースコード
│   ├── Plugin.php             # プラグインクラス（必要に応じて）
│   └── ...
├── tests/                     # テストコード
│   ├── bootstrap.php          # テスト用ブートストラップ
│   ├── TestCase/              # テストケース
│   ├── test_app/              # テスト用アプリケーション
│   └── data/                  # テストデータ（任意）
├── config/                    # 設定ファイル（任意）
├── templates/                 # テンプレートファイル（任意）
└── Docs/                      # ドキュメント（任意）
```

## 必須ファイル

### .editorconfig

```ini
; This file is for unifying the coding style for different editors and IDEs.
; More information at https://editorconfig.org

root = true

[*]
indent_style = space
indent_size = 4
end_of_line = lf
insert_final_newline = true
trim_trailing_whitespace = true

[*.bat]
end_of_line = crlf

[*.yml]
indent_size = 2

[phars.xml]
indent_size = 2

[*.neon]
indent_style = tab
```

### .gitattributes

```gitattributes
# Define the line ending behavior of the different file extensions
# Set default behaviour, in case users don't have core.autocrlf set.
* text=auto
* text eol=lf

# Remove files for archives generated using `git archive`
.editorconfig export-ignore
.gitattributes export-ignore
.gitignore export-ignore
CONTRIBUTING.md export-ignore
phpunit.xml export-ignore
phpcs.xml export-ignore
/.github export-ignore
/tests export-ignore
phpstan.neon export-ignore
phpstan-baseline.neon export-ignore
.phive export-ignore
/docs export-ignore
```

### .gitignore

```gitignore
# User specific & automatically generated files #
/composer.lock
/phpunit.xml
/tmp
/vendor
/tools
.phpunit.result.cache
.phpunit.cache

# OS generated files #
.DS_Store
.DS_Store?
._*
.Spotlight-V100
.Trashes
Icon?
ehthumbs.db
Thumbs.db

# Tool specific files #
# vim
*~
*.swp
*.swo
# sublime text & textmate
*.sublime-*
*.stTheme.cache
*.tmlanguage.cache
*.tmPreferences.cache
# Eclipse
.settings/*
# JetBrains, aka PHPStorm, IntelliJ IDEA
.idea/*
# NetBeans
nbproject/*
# Visual Studio Code
.vscode
```

## 名前空間規則

- プラグイン名前空間: `{VendorName}\{PluginName}\`
- 例: `Authentication\`, `Authorization\`, `DebugKit\`
- PSR-4オートロードに従う