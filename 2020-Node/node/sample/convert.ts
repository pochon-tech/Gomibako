import * as fs from 'fs'
import * as path from 'path'
import glob from 'glob' // yarn add glob @types/glob

const exportCSV = (arr: string[]) => {
    let formatCSV = ''
    for (var i = 0; i < arr.length; i++) {
        formatCSV += i+1 !== arr.length ? arr[i] + '\n' : arr[i]
    }
    fs.writeFile(__dirname + 'formList.csv', formatCSV, 'utf8', (err) => {})
}

const readFile = (filePath: string): string[] => {
    try {
        const data = fs.readFileSync(filePath, 'utf-8')
        const list: string[]| null = data.match(/(({{)(\s*\$t\()(.*)(\)\s*}}))/g)
        return list ? list.map(item => { return item.replace(/^{{\s*/, '').replace(/\$t\(("|')/, '').replace(/("|')\)\s*}}$/, '') }) : []
    }catch (e) {
        console.log(e)
        return []
    }
}

const searchFiles = (dirPath: string, extPattern: string) => {
    const pattern = dirPath+extPattern
    // glob(pattern, (err: Error | null, files: string[]) => {
    //     fileList = files.map(file => path.basename(file))
    //     console.log(fileList)
    // })
    try {
        const files = glob.sync(pattern)
        return files.length ? files : []
        // return files.length ? files.map(file=> path.basename(file)) : []
    } catch (e) {
        console.log(e)
        return []
    }
}

const main = (): void => {
    const fileList: string[] = searchFiles(__dirname + "/html", "**/*.html")
    let exportList: string[] = []
    for (let file of fileList) {
        exportList = exportList.concat(readFile(file))
    }
    exportCSV(exportList)
}
main()