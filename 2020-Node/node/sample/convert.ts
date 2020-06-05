import * as fs from 'fs'

fs.readFile(__dirname + "/html/index.html",{ encoding: 'utf8' }, (err, data) => {
    const list: string[]| null = data.match(/(({{)(\s*\$t\()(.*)(\)\s*}}))/g)
    const list2: string[] = list ? list.map(item => {
        return item.replace(/^{{\s*/, '').replace(/\$t\(("|')/, '').replace(/("|')\)\s*}}$/, '')
    }) : []
    console.log(list2)
    exportCSV(list2)
})

const exportCSV = (arr: string[]) => {
    let formatCSV = ''
    for (var i = 0; i < arr.length; i++) {
        formatCSV += i+1 !== arr.length ? arr[i] + '\n' : arr[i]
    }
    fs.writeFile(__dirname + 'formList.csv', formatCSV, 'utf8', (err) => {})
}