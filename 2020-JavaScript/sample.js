// ネストした配列をオブジェクトに変換する
var sample = [
    {
        a: [
            { aa: 1 },
            { aa: 2 },
            { aa: 3 },
        ]
    },
    {
        a: [
            { aa: 11 },
            { aa: 22 },
            { aa: 33 },
        ]
    }
]

var ary2obj = x => {
    if (typeof x === 'object') {
      const entries = Object.entries(x).map(([k, v]) => [k, ary2obj(v)]);
      return Object.fromEntries(entries);              
    } 
    return x;
}

var obj2ary = x => {  
    if (typeof x === 'object') {
        const entries = Object.entries(x);
        const indexedEntries = entries.map(([k, v]) => [+k, v]).sort(([i1], [i2]) => i1 - i2);   
        return (
            indexedEntries.every(([i], j) => i === j)
            ? indexedEntries.map(([i, v]) => obj2ary(v))
            : Object.fromEntries(entries.map(([k, v]) => [k, obj2ary(v)]))
        );
    }
    return x;
}


console.log(sample)
console.log(ary2obj(sample))
console.log(obj2ary(ary2obj(sample)))

// 特定のプロパティのものだけ抽出
var hoge = {a:"test1", b:"test2", c:"test3", d:"test4", e:"test5"}
console.log(Object.fromEntries(Object.entries(hoge).filter(([k, v]) => !! ['a', 'b', 'c'].includes(k))))