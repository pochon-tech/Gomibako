import sys
import local_modules

args = sys.argv

# 数字でなければfalseを返す
print(local_modules.validate_number('a'))
# 第一引数ソート対象配列、第二引数ソート項目を指定して配列の並び替えを行う
print(local_modules.sort_list_by_field([
    {'score': 2, 'name': 'Bob'},
    {'score': 4, 'name': 'Mike'},
    {'score': 3, 'name': 'Taro'}
],'score'))
# Jsonファイルの読み込みを行う
print(local_modules.read_json('resource/sample.json'))
