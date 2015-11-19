package com.example.midhapranav.eventos;

import android.content.Intent;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import org.apache.http.HttpResponse;

import org.apache.http.client.HttpClient;

import org.apache.http.client.methods.HttpGet;

import org.apache.http.impl.client.DefaultHttpClient;

import org.apache.http.params.CoreProtocolPNames;

import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.net.URI;

public class Login extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login);
        setOnClickListeners();
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.menu_login, menu);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        // Handle action bar item clicks here. The action bar will
        // automatically handle clicks on the Home/Up button, so long
        // as you specify a parent activity in AndroidManifest.xml.
        int id = item.getItemId();

        //noinspection SimplifiableIfStatement
        if (id == R.id.action_settings) {
            return true;
        }

        return super.onOptionsItemSelected(item);
    }

    private void makeHttpLoginRequest()throws Exception {
        BufferedReader in = null;
        String data = null;
        HttpClient httpClient = new DefaultHttpClient();
        HttpGet request = new HttpGet();
        URI link = new URI("http://eventosdataapi-env.elasticbeanstalk.com/?email=a&password=1234&selector=2");
        request.setURI(link);
        HttpResponse response = httpClient.execute(request);
        in = new BufferedReader(new InputStreamReader(
                response.getEntity().getContent()));
        Log.d("Login debug->",in.readLine());

    }
    public void setOnClickListeners() {
        Button mLogin = (Button) findViewById(R.id.login_button);
        Button mCreateAccount = (Button) findViewById(R.id.create_account_button);

        final EditText mUsername = (EditText) findViewById(R.id.username_text_field);
        final EditText mPassword = (EditText) findViewById(R.id.password_text_field);

        mLogin.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                try {
                    makeHttpLoginRequest();
                } catch (Exception e){e.printStackTrace();}
//                if(mUsername.getText().toString().equals("midha") && mPassword.getText().toString().equals("pranav")) {
//                    Log.d("Login debug -> ", "Starting MapView");
//                    Intent intent = new Intent(Login.this, MapsActivity.class);
//                    startActivity(intent);
//                } else {
//                    Toast.makeText(getBaseContext(), ("Invalid Credentials!"),
//                            Toast.LENGTH_SHORT).show();
//                }
            }
        });

        mCreateAccount.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Log.d("Login debug ->","Starting create activity");
                Intent intent = new Intent(Login.this, CreateAccountActivity.class);
                startActivity(intent);
            }
        });
    }
}
