# 1-on-1-Chat-with-Chatbot
Matchup chat website similar to omegle.
Includes Markov Model based Chatbot which stores word-word probability, sentence-word probability, and chatlog-word probability
This was originally meant to act as a system of memory, similar to the structure inherent in neural networks.
If the resulting causal loops of probability can be untangled then it might be able to at least produce coherent responses.
The chat website works wonderfully though, and the database of chat data it produces could be used to train a better chat model.

## Screenshot
![Chat Screen](https://raw.githubusercontent.com/Ethan7/1-on-1-Chat-with-Chatbot/master/chat-screen.png)

## Installation
Make sure to install the chatdb sql file as a MySQL database
Then add your credentials to the provided PHP files to ensure they connect.
Then copy all the files to your webserver of choice to employ this chat program.

Note: The pyscripts folder, index-2 and admin files are unnecessary.