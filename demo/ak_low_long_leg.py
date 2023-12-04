"""
获取当时低位长下影线 stk
@date 2023-1-15
"""

import datetime
import os.path

import akshare as ak
import pandas as pd


def code2symbol(code):
    """
    00：A股证券
    03：A股A2权证
    07：A股增发
    09：A股转配
    10：国债现货
    11：债券
    12：可转换债券
    13：国债回购
    17：原有投资基金
    18：证券投资基金
    20：B股证券
    27：B股增发
    28：B股权证
    30：创业板证券
    37：创业板增发
    38：创业板权证
    39：综合指数/成份指数
    60：沪市A股票代码
    900： B股代码
    730：新股申购的代码
    700： 配股代码
    00：深市A股票代码
    200： B股代码
    00：新股申购的代码
    080：配股代码
    S：未进行股改
    N：新股
    C：上市不到5天
    U：尚未盈利
    ST：连续两年股东收益为负等原因
    *ST：有退市风险
    XD：除息日表示分红
    XR：除权日
    DR：除息除权日
    R融资融券 C创业板 K科创板 300属于沪深300 次新股
    """
    code = str(code).rjust(6, '0')
    if code[0] == 8 or code[0] == '4':
        return 'bj' + code
    if code[0] == '6':
        return 'sh' + code
    return 'sz' + code


def percent(a, b):
    return (a - b) / b * 100


def stock_zh_a_spot_em(date):
    file = date.strftime('%Y-%m-%d') + '.csv'
    if os.path.isfile(file):
        return pd.read_csv(file)

    stock_zh_a_spot_em_df = ak.stock_zh_a_spot_em()
    stock_zh_a_spot_em_df.to_csv(file, index=False)
    return stock_zh_a_spot_em_df


today = datetime.datetime.now()+datetime.timedelta(hours=-9.5)
friday = 4  # Sunday=0
if today.weekday() > friday:
    today = today + datetime.timedelta(friday - today.weekday())
df = stock_zh_a_spot_em(today)

start_date = (today + datetime.timedelta(-10)).strftime('%Y%m%d')
end_date = today.strftime('%Y%m%d')

# 序号,代码,名称,最新价,涨跌幅,涨跌额,成交量,成交额,振幅,最高,最低,今开,昨收,量比,换手率,
# 市盈率-动态,市净率,总市值,流通市值,涨速,5分钟涨跌,60日涨跌幅,年初至今涨跌幅
for i, stk in df.iterrows():
    if (
            int(stk['代码']) < 688000  # 排除科创、北京
            and abs(percent(stk['最新价'], stk['今开'])) < 1  # 势均力敌
            and percent(stk['最低'], stk['今开']) < -3  # 长下影线
            and percent(stk['最高'], stk['今开']) < 3  # 短上影线
    ):
        print(stk.values)
        history = ak.stock_zh_a_daily(
            symbol=code2symbol(stk['代码']),  # 代码不对会报错：KeyError: 'date'
            start_date=start_date,
            end_date=end_date
        )
        _max, _min = max(history.high), min(history.low)
        now_percent = 100 * round((stk['最新价'] - _min) / (_max - _min), 2)
        print(_max, _min, stk['最新价'], now_percent)
        if now_percent <= 20:
            print(history)
        # exit()
