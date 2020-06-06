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

const searchFiles = (dirPath: string, extPattern: string) => {
    const pattern = dirPath+extPattern
    // glob(pattern, (err: Error | null, files: string[]) => {
    //     fileList = files.map(file => path.basename(file))
    //     console.log(fileList)
    // })
    try {
        const files = glob.sync(pattern)
        console.log(files)
        return files.length ? files.map(file=> path.basename(file)) : []
    } catch (e) {
        console.log(e)
    }
}

const main = (): void => {
    searchFiles(__dirname + "/html", "**/*.html")
    fs.readFile(__dirname + "/html/index.html",{ encoding: 'utf8' }, (err, data) => {
        const list: string[]| null = data.match(/(({{)(\s*\$t\()(.*)(\)\s*}}))/g)
        const list2: string[] = list ? list.map(item => {
            return item.replace(/^{{\s*/, '').replace(/\$t\(("|')/, '').replace(/("|')\)\s*}}$/, '')
        }) : []
        console.log(list2)
        exportCSV(list2)
    })   
}

main()