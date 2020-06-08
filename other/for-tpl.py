import sublime, sublime_plugin
import re

class fortplCommand(sublime_plugin.TextCommand):
    def run(self, edit):
        view  =  self.view
        for region in view.sel():
            js_source = re.compile(r'^\s+').sub('', view.substr(region))
            formated_code=''
            if js_source:
               formated_code = for_tpl(js_source)
            if len(formated_code):
                view.insert(edit, region.end(), '\n'+formated_code)
            else:
                sublime.status_message('办法：用两个换行分割开，0 th，1 td，2 tpl')

def for_tpl(str):
    try:
        tmps = str.split("\n\n\n")
        splitChar = tmps[0].split('\n')[1]
        if splitChar==None:
            splitChar = ' '#空格
        else:
            tmps[0] = tmps[0].split('\n')[0]

        ths = tmps[0].split(splitChar)
        tdls = tmps[1].split("\n")

        products = []

        for i in range(len(tdls)):
            tds = tdls[i].replace('\\', '\\\\').split(splitChar)
            product = tmps[2]
            for j in range(len(ths)):
                #i不区分大小写
                product = re.compile(r'%s' % ths[j], re.M).sub(tds[j], product)
            products.append(product)

        return "\n".join(products)
    except ImportError:
        return false
