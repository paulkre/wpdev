const { resolve: resolvePath } = require("path");
const { readFileSync, readdirSync, existsSync } = require("fs");

function resolveEntry(basePath) {
  basePath = resolvePath(basePath);
  const entry = {};
  readdirSync(basePath, { withFileTypes: true })
    .filter((dirent) => dirent.isDirectory())
    .forEach(({ name }) => {
      const path = resolvePath(basePath, name, "index.js");
      if (existsSync(path)) entry[name] = path;
    });
  return entry;
}

function resolveThemeVersion() {
  const themeStylePath = `${process.cwd()}/style.css`;
  if (!existsSync(themeStylePath))
    throw Error("style.css could not be found in theme root directory.");
  const match = readFileSync(themeStylePath)
    .toString()
    .match(/^\s*version:\s*([^\s]*)/im);
  return match && match[1];
}

module.exports = { resolveEntry, resolveThemeVersion };
