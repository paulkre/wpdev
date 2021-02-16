const { readFileSync, existsSync } = require("fs");

function resolveThemeVersion() {
  const themeStylePath = `${process.cwd()}/style.css`;
  if (!existsSync(themeStylePath))
    throw Error("style.css could not be found in theme root directory.");
  const match = readFileSync(themeStylePath)
    .toString()
    .match(/^\s*version:\s*([^\s]*)/im);
  return match && match[1];
}

module.exports = { resolveThemeVersion };
