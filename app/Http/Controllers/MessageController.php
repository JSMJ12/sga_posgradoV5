<?php

namespace App\Http\Controllers;

use App\Notifications\NewMessageNotification2;

use Illuminate\Http\Request;
use App\Models\Message;


class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 5);
        $messages = auth()->user()->receivedMessages()
            ->orderBy('created_at', 'desc') // Ordenar por fecha de envío en orden descendente
            ->with('sender', 'receiver') // Cargar las relaciones sender y receiver
            ->paginate($perPage); // Paginar los resultados

        return view('messages.index', compact('messages', 'perPage'));
    }

    public function create()
    {
        return view('messages.create');
    }

    public function store(Request $request)
    {
        // Validación de los datos
        $request->validate([
            'message' => 'required',
            'receiver_id' => 'required',
            'attachment' => 'nullable|file|max:100240', // Max file size 100MB (100240 KB)
        ]);

        // Crear un nuevo mensaje
        $message = new Message([
            'sender_id' => auth()->user()->id,
            'receiver_id' => $request->input('receiver_id'),
            'message' => $request->input('message'),
        ]);

        // Manejar la subida de archivos adjuntos
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments', 'public');
            $message->attachment = $attachmentPath;
        }

        // Guardar el mensaje
        $message->save();

        // Obtener el receptor
        $receiver = $message->receiver;

        // Enviar la notificación al receptor
        $receiver->notify(new NewMessageNotification2($message));

        // Contar los mensajes no leídos del receptor
        $unreadMessagesCount = Message::where('receiver_id', auth()->id())
            ->whereNull('read_at')
            ->count();

        // Retornar con un mensaje de éxito
        return back()->with('success', 'El mensaje se envió correctamente');
    }

    public function destroy($id)
    {
        $message = Message::find($id);
        $message->delete();
        return redirect()->route('messages.index')->with('success', 'Message deleted successfully.');
    }
    public function destroyMultiple(Request $request)
    {
        // Obtener los IDs de los mensajes a eliminar
        $messageIds = $request->input('message_ids', []);
        Message::whereIn('id', $messageIds)->delete();
        return redirect()->route('messages.index')->with('notifications', 'Mensajes eliminados con éxito.');
    }
}
