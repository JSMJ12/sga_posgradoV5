<?php

namespace App\Http\Controllers;

use App\Notifications\NewMessageNotification2;

use Illuminate\Http\Request;
use App\Models\Message;
use Yajra\DataTables\DataTables;
use App\Models\User;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        $user = auth()->user();

        // Marcar notificaciones como leídas
        $user->unreadNotifications->where('type', 'App\Notifications\NewMessageNotification2')->each(function ($notification) {
            $notification->markAsRead();
        });

        $perPage = $request->input('perPage', 5);

        $rolesPermitidos = ['Administrador', 'Coordinador', 'Secretario', 'Secretario/a EPSU', 'Docente'];

        $usuarios = User::role($rolesPermitidos)
            ->where('id', '!=', auth()->id())
            ->select('id', 'name', 'apellido', 'email')
            ->with('roles')
            ->get();

        $contactos = $usuarios->map(function ($usuario) {
            return [
                'id' => $usuario->id,
                'nombre' => $usuario->name,
                'apellido' => $usuario->apellido,
                'email' => $usuario->email,
                'roles' => $usuario->getRoleNames()->toArray(),
            ];
        });

        // AJAX para DataTables
        if ($request->ajax()) {
            $messages = $user->receivedMessages()
                ->orderBy('created_at', 'desc')
                ->with('sender', 'receiver')
                ->paginate($perPage);

            $data = $messages->map(function ($message) {
                $accionesHtml = '
                <form action="' . route('messages.destroy', $message->id) . '" method="POST" style="display:inline">
                    ' . csrf_field() . '
                    ' . method_field('DELETE') . '
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'¿Estás seguro?\')">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
            ';

                $mensajeriaHtml = '
                <button type="button" 
                        class="btn btn-outline-info btn-sm btn-message"  
                        data-id="' . $message->sender->id . '" 
                        data-nombre="' . $message->sender->name . ' ' . $message->sender->apellido . '" 
                        data-toggle="modal" 
                        data-target="#sendMessageModal" 
                        title="Enviar mensaje">
                    <i class="fas fa-envelope"></i>
                </button>
            ';

                return [
                    'de' => $message->sender->name . ' ' . $message->sender->apellido,
                    'para' => $message->receiver->name . ' ' . $message->receiver->apellido,
                    'mensaje' => $message->message,
                    'adjunto' => $message->attachment ?
                        '<a href="' . asset('storage/' . $message->attachment) . '" target="_blank" class="btn btn-outline-info btn-sm"><i class="fas fa-paperclip"></i> Ver adjunto</a>' :
                        '<span class="badge badge-secondary">Sin adjunto</span>',
                    'fecha' => $message->created_at->format('d-m-Y H:i:s'),
                    'acciones' => $accionesHtml,
                    'mensajeria' => $mensajeriaHtml
                ];
            });

            return DataTables::of($data)->escapeColumns([])->make(true);
        }

        // Vista normal
        $messages = $user->receivedMessages()
            ->orderBy('created_at', 'desc')
            ->with('sender', 'receiver')
            ->paginate($perPage);

        return view('messages.index', compact('messages', 'perPage', 'contactos'));
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
