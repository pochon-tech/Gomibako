import * as fs from 'fs'

fs.readFile(__dirname + "/html/index.html",{ encoding: 'utf8' }, (err, data) => {
    const list: string[]| null = data.match(/(({{)(.*)(}}))/g)
    const list2: string[] = list ? list.map(item => {
        return item.replace(/^{{\s*/, '').replace(/\$t\(("|')/, '').replace(/("|')\)\s*}}$/, '')
    }) : []
    console.log(list2)
})