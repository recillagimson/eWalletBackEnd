<style>
    table {
        text-align: center;
        margin: auto;
        /* border: 1px solid #ccc; */
        /* border-radius: 20px; */
        width: 800;
        font-family: arial;
        font-size: 11pt;
    }
    .table_data {
        border: 2px solid #ccc;
        /* margin-top: 50px; */
        /* border: 2px solid #000; */
        border-collapse: collapse;
        border-radius: 20px;
    }
    .table_data tbody tr td, .table_data thead tr th {
        /* border: 2px solid #000; */
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 2px;
        /* border-collapse: collapse; */
    }
    #header {
        width: 800;
        margin: auto;
        display: grid;
        grid-template-columns: 266px 266px 266px;
        vertical-align: middle;
        font-family: Arial, Helvetica, sans-serif;
    }
    #header img {
        width: 30px;
        margin: 0 0 0 30px;
    }
    #header div {
        text-align: center;
    }
    #header div h4 {
        text-align: right;
    }
    #header div h3 {
        text-align: center;
    }
</style>
<br/>
<br/>
<table style="margin: 30px auto 30px auto;">
    <thead>
        <tr>
            <th colspan="1" style="width: 100; width: 33%">
                <img style="width: 100;" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAASoAAABqCAYAAAALFRz3AAAgAElEQVR4Ae1dCZgcVbUedx9LyNL31iQoRs17PuOCvCBJ1+0JIQRkl8Wwy+JCEFFQRFEMRFRkEdlUFpUgQkK6qnqykZAECCIEkLDIHjLTVdWzb5nsyQTCfd+pqtt9q/pWbzM90z1z5/v6q6Xv+lfXP+ece865NTXyTyIgEegXAjQe/9C7vfXX7ulJbNnTY9A93Trd063Rvq447etaRHd3Pkx3dyygu9sfpLva/kF3tf6d7mqZT3c1/43ubPoL3WnfQ3dYd9Md5p/o9uSddHvD7XTbxlvptnd+T7duuIluffsGuuXN6+mWN35NN7/+K7r51Xm09z9z6aZXrqabXv453fTSz2jPiz+lPet/Qrtf+DHt/vfltOv5H9Ku5y6lXc9+n3auu+TtrucvntO6/qJIvyYqK0sEJALViQCdN++De3rr/71nUz3d05OgYqJayBHVA2mi2glElWJEdVeAqP6QIaq3fke3vPlbuhmI6jUgqmt9RNXjENWVtHv9FbT7hR8FiOoS2rnue7TzmYtp59NztnY+M+eX1Ym0HLVEQCJQMgLv9tZflyEpAVF1Pkz7OoGoHnIlqlYgqvvprub76M4ml6h2OBLVXXS7I1HdQbc13Ea3bWREdSPdyojqdSCqeR5R/ZJueuUXjkSVJqoXrqDd//4R7Xr+Mtr13A9o17MgUQFRXUw7n5lDO56+iHb86zu041/fXt382HnjSp60rCgRkAhUDwK0O37gnk31ezNEZaQlqj5H9VtE+4CoQO0DokqrfffTnQ5R/ZXuTN3rqX1/ptuTf6TbGxlR3UK3vnMz3fr2jXRLmqiuc4iq99VraO8rv6SbXgaiuor2vPRT2vMiU/t4ogK1z5OmnrmIdjz9XSAp2vHUt2j7Py98o23tBROrB205UomARKAkBHZ3Gcc7JBVQ+1ySitO+To6o2h/kiGp+hqhsnqjupNsab6fbGsA+dQvduoERFdinfkM3v+4R1X84onrpKtrz4pUh9qmMNNWZkaZox1MX0vZ/XkDbnzz/jdTqMyeUNHlZSSIgEagOBPZ0JS52iQpUPlft6+vWaV8XGNIZSS2kfZ7at7vNVft2tgBRidQ+ICpX7dvqGdK3vOUa0jc7RJUxpPd6hvS02pe2T11Gu5531b7OdZe4tqln5tBOnzTlkBRtf/KbtG3tN01JVtXxe5OjlAiUhEDfpvpz92wKkBRb7QNpqsNV+/raObWvNaD22XfTHdaf6XYT1D6Qpm6jWzeK1D7XkN7LDOmg9jnSFK/2XU67fat9oPYBSXm2KVD5HGnqfNr25Hm0be25tO2Jc2jbE2eZrWtmH1QSCLKSREAiUNkI7Oqtn8gkKVjtc1S+btclwbVNLaR97Qvo7rag2ue6JexI3UN32HfRHZbrlsDUvq2e2rcF7FOOW4Kr9vW+Bm4J19BNzD6VVvvYat9ltBukKcctgal9GduUS1IXeCQF0hSQ1Nm09fEzaetjZzRYq078dGUjLkcnEZAIlITAnk31D7kkpbtE5flOwUqfY0T3pKndsNrXcj/d1XKfq/Y13Ut38NJU8g6f2rdlw03UVfsybgm9Af+pnhd/RnvWX0m7YbUP3BIcaQrUPmZEh5U+14De7hjQPZJyVD6QpoCkzqKtjwFRnU6b1sxOtj5y6qdKAkJWkghIBCoXgd7e+tF9PYaZNqCnbVPg4Akq34MUSGp32skT3BK81T6HqP5Et5t3emrfra7at+Fm6khTuVb7mNrHOXlm1D5emnLcEYQqn0NSjjR1Om1dM5u2rP4GbVl9mt228mS5Gli5Pzk5MolAaQjQ9gdwX3fczKzyZfymwCXBJSnOd6rpL9TxnbI53ymwT228laaN6OCN/tb11DWiX0ddtW8uZUZ01xsdVvtc36nutO9UHmnKs0u1Pn62J0md4ZBUK5DUqtNoy6On0JZVJ7dIsirttyBrSQQqGoFdm+IH7el8+JXdHRkvdJekPJWvmQuZAd8pXu3zfKe2bvwD3brh92lpajPzRn99HnV8p/7DnDyvopvAdyqfNAXOnZ7K1/7k+bDC5xrPfSR1OnVIavVptGXVqbTl0ZNpy8qv0+YVJ5ltK74+uaJBl4OTCEgEikdgV+tDn+rrXLA+rfI57gh8XB9T+SC2j4XMMNuUQJp6k/OdenUu7f0PH9uXW5rKuCMwn6nMKl/aLrXmdNqyGlQ+RlKnuCS18iTavOJE2vzI8WZq+fFfLB4JWUMiIBGoaAR2tiz85O72B9/MBB8zlQ9IylP5GEmlXRJupdvAJYGzTWWkqV9R1yUhIE29+BPakxWA7NmmOHeEDsex8zzaHpSm1pzhktQqUPlAkjrFkaaaGUmtOJ42P3IcbVp+nJVacexnKxp0OTiJgESgeAR6Nj44anfrA685q3zN99FdXkxfJkuC5zeVvINudxw8/dLU5jcztimI7etl0tQrLFOCJ005K30srg9sU25Mn0+aevJ82r72PNr2hLvK1+as8rl2Kcd47iMpUPlOpM0rTgBpijYtP5Y2LT+GNi07pqf5kSP/p3gkZA2JgESgohGgrffss7N5/vMs8NiJ6bPvptsd504vnQsLlwk4eG4Gle+N65yULkHblBPXl/ZC59O5MOdOFs/nqXxrQZryHDvTJAUqX8Z43vzoybTZsUu5JNXkkBRIU0BSX6NNy46mqaVHb7GWzvy/igZdDk4iIBEoHoFtDQ/gHam/Lt2RNp6zVT5wR4DgY0/lg3AZcO508k55flOQd4qXpl7+eSb4eP2PafcLPEll3BE6nwZ3BCfoGGL5OAN6xl/KsUutOo02P3oKDSepY0GSoqllXwOSoqmlR9HUkiO7rMTMmcUjIWtIBCQCFY3Atoa78A77nlXgge6mcWEkFYjpSyfH4w3o19De9EofJMfjVD5Ijuekcsm4I/hUvn9eQNt8Kp/jfe75S7nG81wkleJJagmQ1CxqL55JU4uPbGlMzIhVNOhycBIBiUDxCGxqvOeA7fafn3Li+RwPdOYz5Tegb/Gt8rHkeF4ql5AMnizwOJ1rKu2OAKt8rjuCu8oHdqmMyudIU64bgmOTyqh7x1KHpJZ6kpRHUqnFRzpEZS8+gtr1R/Smlsw4tHgkZA2JgESgohGgdN4HtzXesYrF8zmrfO/4PdBZquF04LEv1XBGmnLTDIeEyvzzQk7lO5cGHTvBFaH50VM9lc91Q3BIylnh89S93CRF7foZ1ErMeDe5+PC6igZdDk4iIBEoHgEgq+0Nt62E7J1pD3QvjYsv39SrwcBjcO5kgcdgm4LA44zKl4nncw3oLDOCQ1JuwHEmRGYVkNQpaeN50yMn0CYfSTF71FE0tXgWqHrUrp8JUpT3AZI63PtM7zO12IzikZA1JAISgYpGoLX1nn22brjlXv+mDb/NJMWD7J1puxQkxSvAAz1tQGeZEfiAY5HKdzJ4nlOXpMAN4Ti/4XzJLMcmlYekqGVMp5Zet8U06i6oaNDl4CQCEoHiEWhdP2+fzW/deG8mxTDLNSXaXeZK2uPlQufj+bqcFMN8dgRPmvLZpc507FKt4H0OcXyONMVI6kTqt0v5VveozWxSYknKJSmjDoiKWnpsi11fd27xSAyDGpTWfMDUo/9raeQcK6FebRmx+yydrLV0stHSVdP9kJctPVZvGrEbbYPMSRrTp78Rn/zRYTB9OYURgMDWt3779/TOMo4rgidNOa4IAWnq+ctp93M/dDdsYJk7OQ90SC3MVL50jqk1EMeXISmI42P+Uo40BZLUct54nlndgxU+oboHUpTzqaOWQ1QxICr3o5FTR8Bjq6lxyCled7ipk/stg3RZOqHFfmydvGfrZE3KICfNmzfvgyMCODnJqkVg02u/utv1l8qofD2iFC5pkvq+t/UVZO38Lu146tuwUQNnQD+H+rzPuRCZ5pVMmgK7lOd5Dm4IaeO5Z5MqkKRMPUb9H0JNnVxctQ+jkIHbRuwiV1oqnpxCycwgpqXHri6kf1lGIjBUCPS+MvdOsEv1OttfubvKdL/oJcT7NyTE48NkXA90P0kxnylGUq7K5wuR8Uiq2TGeh9mlPBcER92b4a3ueYZzJkU5qp6j7olICoiKmhqZM1RYlq1fyyBHWBpZF0o2JUhVWW0ZxDQX1Z1ctknIhiUC/UAANImeV67+NWx9xfJMOZuJOiQFKl/QFcHNge5k7XySkRTviuAlwmPe54yknIwIJ7jGc4jjY17njq/UkTSVlqRmUDvhre4xVS9NUC5Jgbrnk6Y0h6CApODznrlI/Vk/IKmcqvBwLF39jaWTnVnEMhDklN3G+7ZOronHZ3+oclCQI5EIuAg4ZPXiz27O9j7326WYY6ebWvhC2saFyWQcO71sncx4zhw7QZpazqt8zBWBuSGAC4JLUja4IPikKM4WxZOUn6A8olKpqanvmbr6w6p+vq3LpuxjG2T1IBGU385lxBbStTM+XNUAysEPWwR6XrjiJt+27M9eSv3e59+ljKTaHWkKvM85acoxoHMBx7xdykdSsMrnGs9TS3iV73AKJGWnScpPUOyd9SQnJkEFjg5RAVlRU49eVpUPC0jK0slrbML5jrZOXkhqsV+bet3XGuLRSRsWTImY82eMdo569H/B4SwZV6+2dPUZS1ffz9cefG8bZA38B6tKAOWghz0C3c9dfr3rfX4p7YQt2Z+52NuOnTeeeyqfkxkhvZOMl1aYJcKDVT7O+5z5S6WN506gsRsaw+xSQFIJWNnLqHj8O5WboEDt40jKO0/q0Sur6qEBOdh67FF+4uJztc/SyU1JQy1qR4zWODnIMtRLLJ1sE7ebMdTbOplfVeDJwY4oBDrWXXqdQ1LrwkgKtrsKc+x0UwqLXRH8MXyuvxRT+cBw7pGUwSSpzDvjkpDPFsWrekKSyhAX+XnVPMCUQa7LRyCWpi57x4h9pj+TaopHDzQ1cle+vuxEbHgY/PoDlqxbsQh0rPveNbCBKAuRyeQ/z6QVzrgiZAKOIVsnc0WAJHjMFSETbMz8pZjKByt8HkkleP8ol6QyZMOkJZ6s2L2CjtdWLNhsYFY8dko+4rA09fes/EAcwZiXp8+dVmL65weiL9mGRKAcCHQ8fdHcDm+TBjetMGTsZJs0eDmmArnP3fQt6bznghCZo6jt2KUgji9MmgojKUZIjKzYdWFHS4vOLQdOA9ampZFkLtKwy7RC0KiRU3P1axlkzYBNUjYkESgDAh1PXXhNh+PUeUEmrfDjZ9M22DgUjOdsT75AwLHjM+XzPndX+WwntxSTpnii8ktTGXtUYSSULXmJ61UsWVkJcmVOstBiAypJBX8rSY3Mzd0/OTZYR15LBCoJgY615//EkaQ8u1RG5ZvtbneVJinX+9wlKc4VwfGZAklqlidNsRAZlg3Bb5vKkFRpklM+0rL02G8qCd+atWtnfNgyIDYvY5Tjz00jtrLcA3Z9tsjTfL+B87XlHoNsXyLQXwRa155zedsT53jbsLtZEfiNQ9N2qUdgqyvPsXOZm/c8k04YMnX6pSknbQvnjuA4cmb5R4mlo3yElOf7G/qLyYDVN+PkjAAp8H5NnVb8sE8PWGc5GkrqU79s6+rekLG8DwHQOarLryQCFYFAy2Nn/bTVU/lY7nO2cShb5QtKU02OO4LIZwqcO7mVvrQjpxOzx/lGlYWknFVCSyO/HVBgwW8p+ZD6qcY4OeiN+OT9Cm3c9W8SS1OWTv5YaDsDUc7SY4+FEBWAdiPfB10/5SMw1+Bn/bIp+/DlCjlvjM86INgOXIvqispZ/6gbX2hZUX241xSfeeDGFZM+Jmqnv/cQQrWFfhRFwQihgn8/ucYW0mdo22PGjDmg2Dph/cMcRG3tt99+KKzOQN1vfuyMOS2rZ7/PUre4q3zO7sauJMV2kQFpyuczFQw69ojKk6YyITEeUTlxe+UjqbS0pUdvKxkbyDxgJ8hRtkGW2rraHXzBbYN0mzpZamrqmQ2rVCzqaOOKOhSsx1/bD321X24Ioj5z3UsumnY633/g/DW+Lkhgge8dSRDmy5cr5DwZJyeJ2oI+gvVNjbyUXVZ9K1gOrr20N7yEWsj5DksnT5s6uaZpcWyqqN1i72GM2zDGtMjPdozxaowx5DEqOstFJBI5PKS/jpoasTNvJBL5bkidx4udM0LoDlFbCKG/FdtWKeWbVs++rOXRU99zs3UKHDu9WL4m2EGGy3vuj+fjnDvBwdNg8XuDTFSuY+iNRTthN+rRb1kaac9+YcIkI3WLqan3rZ3nD01JhbygXrt6KQ+oP3Ua41MOMHXSETYvkBpZ+8OYqHxkZuukwdJj32fzLuVYIlHxxGYhhE4vpm+M8T0iooB7CKGwXN4fRgg9IaoXiUROKrT/MWPGHIQx3iJox5owYUL6N1Roe6WWS608+azmlSe/5/M+T6cV5hPh8Rk7YZWPX+njjOgOUQ0JSblqoB79a8FkZenqLWEvct77BjHtBElvpWPp0ZvC6pQimZT6QPl6pkEeDhuTrUWPZGVHClExLEydmLZWl54/w6GQ4wAQFSOtghwCgQwwxl0ConDaAWknbNxhkhhC6M2ampqCYkAxxg+E9H1JWL/lut+88qTZzStP2paV+9yXGcFV+fJJU65ENXRE5aiCOrk/b/JL21CvZT/cUo+2Thazh5LLPtWcOGxItog249GrwubGJ/0aaUTlYKKR7akE+Ql7foUeB5Co3scY5028pijKmSFEwQivMxKJ7B82foTQQlH92tra2WF12H1FUT4tqgtEN2HChKLtl6zd/hxblh9/ZtPy4/vYFuz+TUPFdikn8DgdLsPUPo6kBss+JYgNtDT13tAsJ6mE+kVLJ3mCevMH/ZoLp01koFs62RVCCptZmcE+JnUSaqcydfIHNp6qJSqNdFkGWSn4PGEZZEPI8/Cpg6mEeiHDoZBjCFGZGOP6wGcxxngNxrhd9LJ793r33XdfJVe/iqLEc9RnUtVxYW1gjBWM8Q5BG71gcA+rB/cRQusF9WgkEjk+V71yf9e89LgTmpYfuyOdY8rLjBB0RXDTuGTbprKkqSEkKs/IvlBIVpZOHgn5EffaRuzaloXTPwlgb7xj0sca49EvueEp6ka+jm2Qv7MH0rpgRoT/zneukXWs3GAfG1xC9r2Y3NjS/lTVS1RqTsOw/VBsDEiOXs55MQ4GeT9lqF8t9NmIiAohlHMlByH0fYTQJtFLryjK78L6PuCAA8YghLbx9RRF+TdCaA9/DyH0l7A24L6iKDfw5bnzUPUzEomcwJVj0htVFOWRXH0N1ndNS752TGrp0Vuy0rf4EuJ5JBWQptxEeJUhUbHVwCzXBXPtjI9bBnmXe2HZD9h856HcAcLJeOyb3mYLO3hpCshM0J7TrqmRsjt5hv04mo2pnwgbl63FXmX1hitRsfnB0dTD1WBLJ2ZwcYSvy5+XQlRQv7a29gjRi48xtvn2+XNFUb4VrKMoyncwxv8M3O8dNWrUWL4uf64oyr4hdq7dY8eOdf4p8+Vramo+gjEGKTFNUN75XlAHA2WH7NI0Zk1LLTl6L2zBnt7d2Gc85/ymnJU+V+2rRKICwmpcpJ6WBjMZrztM9PKahvrTdKEcJ5BeJanFzuOL2HESE7Xp3NPUB/myg3n+RnzGfqHj0jNuACOBqAD3RjctjlDl5212uZ5RqUQFbSqKskjw8tP99ttP6PKCMX48WF5RlM8ghH4evI8xzpm7W1GUHwrqABHdG5wv2M5CyqbNBcE6Q3WdWnLUoanFs3oyO8nw+c+nc6lc+Ng+cE3gJCrHM30QfKgENiomUbnH6Dvm/Ikfd7BMGup00cub0qMl5xmHhHaiNuGeqav3DNVDNOfP+HjYuKwRSFTwHGyDXB+CydpCdvLpD1FhjC8SEQBC6CvB3wg4WGKM9wbKO1JwJBI5NHAfCOfRYBvBa4zxU4J6YL/6LCs7evToiSHSlBVGqKzuUB1b9OmHpBbP7PK7IoikqUyCPG9jhkHxSPeTUW5CTC7yMi6ESVR2gRKV6GHkIirbGLqkdbmJiqSdPkeKRAXPDlQ828h26gXyso1pRPR8+Xv9Iaowu08kEsnaKhwh9G0BqXyPjQUh9HLg+93777//OPa96KgoypGBOky1e4yVB3ubqIyiKBWdZjelzzjUrj+iAzZqyITKML8pvzSV3qzBF+OXm0CKIZt+ldVVcB2pqWlOHDZO+B/ViHW16NFD2AMr5lipqh84fQrn6gRNx55hcxxJRAVzNjX1BhEuSZ3kTSPbT6I6UUQCIqLCGL8SLDthwoTPsWeGMZ4b/F5RlMvZ92FHRVHuC9aD60gkcgTYqzDGOwXfvzxjht/BOaz9obxvx4+cbNcf3pIJPOaIKh3b53mk+0gKsiZUCFFpKk1jGBaOYWpqeypBflSoYZU1aOnRQ0Q/fLgH6YhZucE+gsE/dFwGWcLGM9KIqilRd7gQF01dwDAJO/aHqDDG1whIgNbW1n6B72/UqFGTBOVe58uEqH//4suIzseOHfsJjLFvJdHrayPGWBP0Cyt9J4jaqsR7zYnY/9iJ6S3pXWWMOv9WV0BYWSRVoUSV1KYfndOPKk4abCN6Eb+yl+uhNCyc/knhD9+1Ub2Tq245v8sl6dlG9HrW90gjqp4VU0eF7E79EsMk7FgqUe2///7gYd4oIIKdQS9xjPH3BOWCecw+gDHuDpTbW0igMLhEBOoxFTDriBBaGoZFpd7fWK9+1jbqUrBpQyb4mMX2VRFRAcCmRn4eRi7p+wbZZOpkPkhMuR6KZwsSuTyAi8Iu8MfKVb9c37nuFOKYRVMn57N+RxpRwbxNXX01/Zwz+cOSDJOwYylENXHixI8jhG4PIYfVwb5Exuzx48dnpZFGCF0vaDNv+tvRo0ePxhh3CupmEVUluSMEccp1/b1T//vsZ++dlk1UPmmqctQ9XvXMmlcxYTS2QZaY9RlP9GBjZg5P6GTisIOD5Qfj2jZiNwpeRse/y05MTccpjkSisgzyuACb1nzPRURUkUjkLnj5g39g8xk3btypGOPXw0gBQmT4PseNG/fVYFmEkDCjBMY4GiyLMd7Atxd2jjH+pqBukKhuDqtfyfcVRfk6zO0rnz+Qrrt3aoasfCRVWepeTqICsBvisSn5cpzzP2hbI7dSgYRkabF7+XL8uZ0Yml1Uc8Ufpv01ampqRiJRmRpZyz8j51wj7fleQBFRFfDCBwnAuVYUZXmwvxAv8jAXlw8K1D/IqJDl7hDsB64RQk/mGHtHLidSUXsVcu9jGOMONi8gq6f+dJhLVj6iqkxpCggrFEfwnzHj6gWmXlh8GHgyNzzwZZ+THjiBZv3wMyrFWwWndAgdZXFfJBOxg8PscLYWe55vbSQSla2pzwefl6mree2JA0hUZm1tbTpW1HseHwWJiL1k7IgQms4/L/4cYzyfleOOoaExfF2Q3oLhOFwbBTlA8+1VwjlCKMbNwfmHcDBIVvdMDRjRq5CoeIBtLTrb0sma4I84eG3qseTL82eMZnUbEioOluGvwdeKlR2Mo52ICZfgYUy2rt7Oj2GkEVWn47Gv9vDPxznX1LTLBo8Pfz5ARPV6JBLJyqiBEDo2+JJhjN9DCP0CIXSV6OMFQvskNkVR7MmTJ3+UH3fYOcZ4haDPvJJlWHtDfR9jrArmQx2yuvswjqyqnKgY0GZc/Yqpk/uzfswZKQlsPb6QAksnz4WWN0iCtV3uY3z27A9ZumqHjiWQu32kEVVYdIKpk4fzPZv+EBVCaDPG+PdhaVkQQkIfJ9GLl+8e+EXlmwt8jzG+P9iWoihCm1gh7Q11mdraWiTw6HeI/HOfHU/XpclqmBAVAxxcFMx46M4yfbxUlSutivsfe9qgbFUF+dnDSMrU1CfZ3NjR1skXhOU1cg4rU+ixUVdPE7UFZBhso9+piLXc2ROC/bFr2yC3isZo6eqPWZmwYwhRtSGE1oZ8loG3N0LomHxpVTDGLUHSKPUaVhnD5sDfH25EBXNDCP0sDLcvf24CffoukKyGGVHBxNsf+PK+lkbWi37cST12InvwkK1PlHOd1TM1YkNbrHw5jrDTTY4daEDtg7zdvj8ItGZj5I+QccBXsIALy4hdwrfBzhsFyQOHgqjAmVe4pZlB3rfjsSn5pigiqnxpXvK1Cd8jhM4Ie7lKvN8BWRPy9T0cicrD8wKM8RsIod7g5yufP7D3sdsO3VWhZJXXTprzmVpaVGUvHX80tagvat2Mxy7jvw+e2zp5Km/60ZwjCf8yaaifsnXiy5vF9w99i2pvXHHsxyxN7ePLeufpnFWieqJ7tq7+I9iOrZO9Il+yoSAqSyN3BMfnXT8hmk/wXrmICmOcKJGQfPapQBunBscfvB6uRBWcZ/AanLRNPfpmpZGVUKrfGD+k4O1+zPhXa8U/cL+6QOfVfNDSyQvisq7zpW2Qv6yLR/8rCF5/roGkLEMs9bGxmPG6w8P6CJEY97bESTq+LKwuuw9+ZpZOtrH+uKPQ43uwico2YhdxY3L8ydi1zTnAsvmIjuUgKkjrixASbaDwkJdyBdKu5PvYAZICAsubYmikEhU826Z49EBTVzdUDFnFVbMpHvXnFbM08gtLJ6/lcuLkf6iNi2Kz2I+aP9r6tKwdRZoSUYgl28qXE5yvLbRvfhyi8yY9NlWozvCG/zybH1o6uVQwRmrpsRfb7stP6Gb9jNGWHntG3IZ6i2jcg0VUsMefpZPwzTdYxLpokIF75SCqcePGOQ6KQaJBCOWMiOCHhhAShcVsnjQpd1TESCYqwK912ZR9KoasFk1LR4s4z7bJSZ6X3kn4XVsn16xfP+Uj/IPnz2EjTvCxEb6EcTXKl2Xn+VRAr61mW4vmjXhnbQaPQA62od6RyyYF/YA6KMzJzDXoEg3pFc4RdtzRiS9wlqtak9RIXQ6Vc2fQ54zVLTdRuZky1B9bOglfAdXVve8UsediOYhKURSR42UDw6mQo8h/yCO+nHnWRjpRAbZOgvy+tDMAAAstSURBVElNfWFoJatp/sWP/9x89L5C6cN9GedbGjmW7bLrBK8myDmWTl4WvcCw7VKuH1Gu/+L+9mJJ01BvaFoWPTBXey6okz+a1NSjbV2db+kxkZrlU2nAORVE3HztwvepRN3F/nEF4wTV1yxNXWDq5HfwARXW0tXXctbRyHVhffebqAyySbCxw0qQ7GydNFpGvg08IAdV7Jth4xPdH2iiikQi44OSlHftc30RjSVw70OisWGM07mmAuWdy2okqvHjx0/3Mj08izEekM/kSRM2GL89ZEhWAy1dfSRLkLA09U85X6y0uqTuzfdDN3U1nchM9CMAb3TY7aWw/tKksBFSw1hx9c+2Tm4y3Xi9O8FIbbu2r3wqZZqobIM0FLtDs6mRRJHjTfcXrGfr6jII2BZhA/f6TVTpZ5XGLnQswbGZGunlA7PDxhi8LyKD/qz6IYRgdSrLIC4KQg6OJXiNMb5R0NZOiEEMlmXX1UZUkHwQIfSeYJ5ZGBZbZtLE8fTBaw8eVLJKxtXloLGx55E+JuPkSktPq30F/7CDP3QIak03mufE0mK/zqrfj5esoLY0ss6KTy86ET+oS6YRW1pQHznmYGvkX41/JwflgmaoiMqMk2ethFhlzzVe+G6giQpj/JzghSpK7WNjhlAbQVuQT+osViZ4rDaiEmWWEM251HufPqiWLr5hcCSrZDy6NKcHgLVk+iE5t1DK8QLCC2xq5OVcNq3gjwGuG7W6Iy1dtfpLAIXVj72YEwDRAAP3khq5rrC+sqQZSHdzUyH5x4eAqN5K6dHQlzYAgfByIIlq3Lhxnxe9UJFI5M/Czgu4yQfkcm0/G1a1mohKUWCLwmzpc6DvKQqmD80rr2SV1NS/0HkFZE19Iz77o7ZW9wNLIy1FvJDbUga5rlSnTXBxMLWiVcFSpb7GXAbwsB8ufx+88pMa+aOlCeLissm8FzayKNQeBv2Um6hMnXRCWJOtRy8z69WCMgrw8xedDyRRYYyvEL1kiqJ8UdR3IfcURblT1OaYMWOE0m01EZW34UW/VTwRPqJ7911dLrIif8+ySRXycC1DPd7SYn+yDSeiPrid0ruWEVtuaeoVJheEXEi7YWWcsBzDCRpuLYIkSyGsnZBzqxDpJmyscB/qW4unq1acfBtWSMHultTJnZYe+42pkTmmHptaCnm3LpgSaYyTg4If0Vga47MOCJYLu94Qjx4ocjAVtVvsPdilRfAJtQHlat/LX5XVXq46+b4Db3TB+KAP4RjLMYZ8Y+zP9xhjSJk8KGR14ASF3vajLw6wzWra7QOSSQV+4C2Lp38SfJzAe7VcP3h4WBDFb8ajx1h67Baw6Vh6iHsAL71ose2O/5em/i1pRC+yxZkqg6S21kpMz8oQ2Z8fzHCqm3w4drCVUGcOpzkN17koijITY/zuoJHVeIXe+eMvDRBZRe8aEJKqhIfbtCQ6yV4Um2Ub6jecHFlx9QJbrzs3FScn2YunTg5OdP09Uz5ixaPgzBiUBINktddKkD8MlJNpJWDV3zE0xaP/ZevkKktX91g6eRv8afrbpqxffgRg0UBRlIdDgsDDgsNLvl9bi9Yuv/n/numPnxWYUMqPTBX00FQfPazABYKdKT12tTn/K0JVoAqmOiBDtAxyhKWTZqZ6J4v0pxqQQchGqgoBU4v+rTSyIumNVKpqwuUabGoJmWAZsYXs5ctz7LU1cv1bicNyblpZrrEOVbt2nBxlGY6a7Uicpk52D3ZSw6Gau+y3fwiANtMYV28rhqySGqnKvPP9Q6rA2pbmpFoRh8Pw9i4410iLbZC7wTBdYPNVWawhHjsmuJ+jk3pa2u6q8nkO1aCBrExNvb0Qskrmia8dqjlUVL+N+tT/tozYhjxSFW/H6rMM8uBwki5gpdA2yOWQ3iYLB029b6BWcSvqwcvBlB2BjQ9OHWVpamNOsopHf1X2gQyXDiDlTLLgkCHecTOWtBLRb729kEyoRizsRXXEyXKqEVgh5ckYJMhNkO6lGuc1TMcMG6nChquwOeuuavkcGzvo3TCiapQkVdpPNaWTQws0tPtfajfzwhpTU7/eGJ9yQGm9D04tewH5gmXUXWcZ4Q68EMeYle9ncIYnewlBAGN802C5Ggx0P0/f9dUst4WkRn4RMlV5uxAEQFxNamo/Yg5VUA2fSGrqFRuNGZ8opM9yloF9CZNx9Rt2gtxlGiSVJTn5JSmz0YjNKud4ZNvFIwC5shBC2weaQAarvZsu/YKPqCRJFf8bCK0BgcqWFo3nebGzJKtgeVOPvWrr6gNJjXzHNMg0fkPT0M5L/AJsTSAVghe85XjEk5csnewKjinrGkJ/NDIX0vWU2LWsVkYEYF/DwSKVcvQz59RJPFFV5T6IZXy8A9M0bPJg5civnvXS+yUUAZHF2kDighxVybh6dVJXzwXHVSCYdx6KfWbDgikRIDM+EBOu34hP3q8pPvPApnh0kqeinuYQEqhxOvmrZZB1lkG6ihxPL4T3QIjOwKAlWykHAmPHjv1EOQhksNo84+hPU1OLvt9YwO5F5cBvRLVpGuoFA0tYAeN1XoIb0PK9pqbeEJZRdEQ92CqZLEKoebCIZaD7OfuYz9CGRdG5VQL18BimFVdnWjp5pUjJRSBZDSjxFNa+RjYldXIlqIjD42mMnFlAIjyM8Y6BJpHBaO/zk2pvGDlPqsJmmkwcdnASVC5duH1WYcQxWFKUQRKw6UZ/s0NU2CMYccMBWxVC6Gpvo1bYrLWiP7BSqSiKXJyphF9qwwMqtuKxsy1NXRayBdagkxaEu1iG+ripkx8ll0xVKgEnOQaJgESgQhBILpiqQH5xSyOGpatNg6sewsqduszSYldAHq8KgUQOQyIgEah0BJJLDv1cUlN/YGmxZaHbiJWq/mlAhOqypBb7tb24LiutTaVjI8cnEZAIVCgCkFwQsnxCplRI+wtBzrZO1ng7Rr/tesWrpucdv9E2YNst8ripq7c5aYI19czGePRLMvauQh/wEA1r4sSJHx+MzxBNT3YrEZAIVDMCkUhkDsZ4w2Cs3Hl97EQI/W3UqFH+7dSrGUQ5domARECMwJgxYw7AGF+LMV6ZK/smxjiBEDpO1ArG+Khy7dtXAPGtEo1J3pMISASGEQKiHW1ykEPf2LFjo8HpY4xX5ahT9k0dYKeb4JjktURAIjBMEMAYkxIIJmu/QYzx6yW0M2AENm7cuKnD5JHIaUgEJAJBBBRFObNYgkEINQXbQQi9VGw7A1k+EokcGhyTvJYISASGCQKed/nmIknj/uD0I5GIcFPWItstVcIya2pqPhAck7yWCEgEhhECGOOfYIzfL4RUEELvALkFpz9hwoR9MMZLCmljgMt0KIry9eB45LVEQCIwDBFACE2KRCJzMcbzwj4IoQunTJnykVzTRwgdE1a/DPcviUQi43ONR34nEZAISAQkAhIBiYBEQCIgEZAISAQkAhIBiYBEQCJQvQjABg61tbVfGDt27LTB/Oy///4jagfw6v2FyJFLBIYYAYzxwRhjc4BX54pxPbhyiCGQ3UsEJAKDjcCYMWMOwhg/jjHuRgj1gsOmoii/q6mp+ZBgLPsoipIaQpJihHaeYGzylkRAIjAcEYhEIhNyEM8/gnNGCF1WASQFZLUiODZ5LRGQCAxTBCKRyFl5iMeXGlpRlBvylGcST7mPLw/TRyKnJRGQCAQRQAj9IBfxKIryRb6O56VebhIqpP1/8eOS5xIBicAwRgBjrOYgKoiR8/2BqogQKjb2rxDiKaoMQuhbvoHJC4mARGB4I4Ax/mmQrBBCmyBsRjRzjPFpGOP2YJ1BvL67GoOM/x9qPojboZNYEAAAAABJRU5ErkJggg=="/>
            </th>
            <th colspan="2" style="text-align: center; width: 33%">
                <h3>
                    Squidpay Transaction History
                </h3>
            </th>
            <th colspan="2" style="text-align: right; width: 33%">
                <h4>
                    Date: {{ Carbon\Carbon::now()->format('m/d/Y') }}
                </h4>
            </th>
        </tr>
    </thead>
</table>
<table class="table_data">
    <thead>
        <tr>
            <th>Date and Time</th>
            <th>Description</th>
            <th>Reference No.</th>
            <th>Debit</th>
            <th>Credit</th>
            <th>Balance</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
        <tr>
            <td>
                {{ $record['manila_time_transaction_date'] }}
            </td>
            <td>
                {{ $record['name'] }}
            </td>
            <td>
                {{ $record['reference_number'] }}
            </td>
            <td>
                {{ $record['transaction_type'] == 'DR' ?  number_format($record['total_amount'], 2) : '' }}
            </td>
            <td>
                {{ $record['transaction_type'] == 'CR' ?  number_format($record['total_amount'], 2) : '' }}
            </td>
            <td>
                {{ number_format($record['available_balance'], 2) }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>