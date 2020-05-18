import traceback
# 数字でなければfalseを返す
def validate_number(number):
    try:
        x = int(number)
        if x < 0:
            return False
        return x
    except:
        traceback.print_exc()
        return False

import numpy as np # pip install numpy
# 第一引数ソート対象配列、第二引数ソート項目を指定して配列の並び替えを行う
def sort_list_by_field(items, field):
    scores = []
    for i in range(len(items)):
        item = items[i]
        scores.append(item[field])
    index_array = np.argsort(scores)
    index_list = list(index_array)
    result = []
    leng = len(index_list)
    for i in range(leng):
        index = index_list[leng - 1 - i]
        result.append(items[index])
    return result

import json
# Jsonファイルの読み込みを行う
def read_json(resource_path):
    json_file = open(resource_path, 'r')
    json_object = json.load(json_file)
    return json_object
