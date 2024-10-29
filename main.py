import telebot
from telebot.types import InlineKeyboardMarkup, InlineKeyboardButton

TOKEN = '7226136496:AAGPQ-xThvg39C7o8miMEKwMhpQBFngO9rw'
bot = telebot.TeleBot(TOKEN)

WEB_APP_URL = 'https://ukm4ks.github.io/poro/'

@bot.message_handler(commands=['start'])
def start(message):
    markup = InlineKeyboardMarkup()
    play_button = InlineKeyboardButton("Open", web_app=telebot.types.WebAppInfo(url=WEB_APP_URL))
    markup.add(play_button)
    bot.send_message(message.chat.id, "Нажми 'Open', чтобы открыть мини-приложение!", reply_markup=markup)

if __name__ == "__main__":
    bot.polling()
