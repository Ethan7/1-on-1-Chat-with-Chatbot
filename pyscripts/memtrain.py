# By Ethan Hughes #
# 6/25/2019 #
import pickle
import sys
import threading

vocab = {};
maxlen = 0; #Set permanent value

def synchronized(func):
    func.__lock__ = threading.Lock();
    def synced_func(*args, **kws):
        with func.__lock__:
            return func(*args, **kws);
    return synced_func;

def responsememorize(words, response):
    global maxlen;
    wordlist = list(dict.fromkeys(words.lower().split(' '))); #make list from spaced words and remove duplicates
    responselist = list(dict.fromkeys(response.lower().split(' '))); #make list from spaced words and remove duplicates
    if len(wordlist) == 0 or len(responselist) == 0:
        return;
    for word in responselist:
        if word not in vocab:
            vocab[word] = {0: 0};
            if len(word) > maxlen:
                maxlen = len(word);
    for word in wordlist:
        vocab[word][0] += 1;
        for respword in responselist:
            if respword not in vocab[word]:
                vocab[word][respword] = 0;
            else:
                vocab[word][respword] += 10;

@synchronized
def synced(args):
    pickle_in = open('vocab.pickle','rb');
    vocab = pickle.load(pickle_in);
    pickle_in.close();

    line1 = args[1];
    wordlist = list(dict.fromkeys(line2.lower().split(' ')));
    for word in wordlist:
        if word not in vocab:
            vocab[word] = {0: 0};
            if len(word) > maxlen:
                maxlen = len(word);
    line2 = args[2];
    responsememorize(line1, line2);
    for word in list(vocab):
        for activation in list(vocab[word]):
            #vocab[word][activation] -= 1;
            if vocab[word][activation]/vocab[word][0] <= 0.01:
                vocab[word].pop(activation);

    pickle_out = open('vocab.pickle','wb');
    pickle.dump(vocab, pickle_out);
    pickle_out.close();

thread = threading.Thread(target = synced(sys.argv));
thread.start();
thread.join();